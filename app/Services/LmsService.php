<?php

namespace App\Services;

use App\Jobs\ProcessLessonVideo;
use App\Repositories\LmsRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class LmsService
{
    protected $repo;
    protected $disk;

    public function __construct(LmsRepository $repo)
    {
        $this->repo = $repo;
        $this->disk = config('filesystems.default');
    }

    // --- PASS THROUGH ---
    public function getFilteredCourses(array $filters)
    {
        return $this->repo->getFilteredCourses($filters);
    }
    public function createCategory($data)
    {
        return $this->repo->createCategory($data);
    }
    public function getCategories()
    {
        return $this->repo->getAllCategories();
    }
    public function getSubCategories($id)
    {
        return $this->repo->getSubCategories($id);
    }
    public function getCourse($id)
    {
        return $this->repo->getCourse($id);
    }

    // --- COURSE LOGIC ---

    public function createCourse(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['thumbnail'])) {
                $data['thumbnail'] = $data['thumbnail']->store('courses/thumbnails', $this->disk);
            }
            if (isset($data['demo_video'])) {
                $data['demo_video_url'] = $data['demo_video']->store('courses/demos', $this->disk);
            }
            $data['final_price'] = $data['price'] ?? 0;
            return $this->repo->createCourse($data);
        });
    }

    public function updateCourseDetails($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $course = $this->repo->findCourseForUpdate($id);

            // 1. Handle Thumbnail
            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                if ($old = $course->getRawOriginal('thumbnail')) {
                    Storage::disk($this->disk)->delete($old);
                }
                $data['thumbnail'] = $data['thumbnail']->store('courses/thumbnails', $this->disk);
            } else {
                unset($data['thumbnail']); // Keep old if not uploaded
            }

            // 2. Handle Demo Video
            if (isset($data['demo_video']) && $data['demo_video'] instanceof UploadedFile) {
                if ($old = $course->getRawOriginal('demo_video_url')) {
                    Storage::disk($this->disk)->delete($old);
                }
                $data['demo_video_url'] = $data['demo_video']->store('courses/demos', $this->disk);
            } else {
                unset($data['demo_video_url']);
            }

            // 3. Price Calculation
            $price = (float) ($data['price'] ?? $course->price);
            $discount = (float) ($data['discount_value'] ?? $course->discount_value ?? 0);
            $type = $data['discount_type'] ?? $course->discount_type ?? 'fixed';

            $final = ($type === 'percent')
                ? $price - ($price * $discount / 100)
                : $price - $discount;

            $data['final_price'] = round(max($final, 0), 2);

            return $this->repo->updateCourse($course, $data);
        });
    }

    public function deleteCourse($id)
    {
        return DB::transaction(function () use ($id) {
            $course = $this->repo->findCourseForDeletion($id);

            // Delete Course Files
            if ($path = $course->getRawOriginal('thumbnail'))
                Storage::disk($this->disk)->delete($path);
            if ($path = $course->getRawOriginal('demo_video_url'))
                Storage::disk($this->disk)->delete($path);

            // Delete Lessons Files
            foreach ($course->lessons as $lesson) {
                $this->deleteLessonFiles($lesson); // Helper function niche hai
            }

            // Delete Resources Files
            foreach ($course->resources as $resource) {
                if ($path = $resource->getRawOriginal('file_path'))
                    Storage::disk($this->disk)->delete($path);
            }

            return $this->repo->deleteCourse($id);
        });
    }

    // --- LESSON LOGIC ---

    public function addLesson($courseId, array $data)
    {
        return DB::transaction(function () use ($courseId, $data) {
            $lessonData = [
                'course_id' => $courseId,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'order_column' => $this->repo->getNextOrder($courseId),
            ];

            if (isset($data['thumbnail'])) {
                $lessonData['thumbnail'] = $data['thumbnail']->store('lessons/thumbnails', $this->disk);
            }

            if ($data['type'] === 'document' && isset($data['document_file'])) {
                $lessonData['document_path'] = $data['document_file']->store('lessons/docs', $this->disk);
            }

            if ($data['type'] === 'video' && isset($data['video_file'])) {
                $lessonData['video_path'] = $data['video_file']->store('lessons/raw', $this->disk);
                $lesson = $this->repo->createLesson($lessonData);
                ProcessLessonVideo::dispatch($lesson); // Background Job
                return $lesson;
            }

            return $this->repo->createLesson($lessonData);
        });
    }

    // **NEW: Single Lesson Delete**
    public function deleteLesson($id)
    {
        $lesson = $this->repo->findLesson($id);
        $this->deleteLessonFiles($lesson);
        return $this->repo->deleteLesson($id);
    }

    // Helper to avoid duplicate code
    private function deleteLessonFiles($lesson)
    {
        if ($lesson->thumbnail)
            Storage::disk($this->disk)->delete($lesson->thumbnail);
        if ($lesson->video_path)
            Storage::disk($this->disk)->delete($lesson->video_path);
        if ($lesson->document_path)
            Storage::disk($this->disk)->delete($lesson->document_path);

        if ($lesson->hls_path) {
            $hlsFolder = dirname($lesson->hls_path);
            Storage::disk($this->disk)->deleteDirectory($hlsFolder);
        }
    }

    // --- RESOURCE LOGIC ---

    public function addResource($courseId, array $data)
    {
        if (!isset($data['file']))
            throw new Exception('File is required');

        $path = $data['file']->store('courses/resources', $this->disk);

        return $this->repo->createResource([
            'course_id' => $courseId,
            'title' => $data['title'],
            'file_path' => $path,
            'file_type' => $data['file']->getClientOriginalExtension(),
        ]);
    }

    // **NEW: Single Resource Delete**
    public function deleteResource($id)
    {
        $resource = $this->repo->findResource($id);
        if ($path = $resource->getRawOriginal('file_path')) {
            Storage::disk($this->disk)->delete($path);
        }
        return $this->repo->deleteResource($id);
    }
}
