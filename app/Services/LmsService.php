<?php

namespace App\Services;

use App\Repositories\LmsRepository;
use App\Jobs\ProcessLessonVideo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class LmsService
{
    protected $repo;
    protected $disk;

    public function __construct(LmsRepository $repo)
    {
        $this->repo = $repo;
        $this->disk = config('filesystems.default');
    }

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

            if (isset($data['thumbnail'])) {
                if ($course->thumbnail) {
                    Storage::disk($this->disk)->delete($course->thumbnail);
                }
                $data['thumbnail'] = $data['thumbnail']->store('courses/thumbnails', $this->disk);
            }

            if (isset($data['price'])) {
                $price = (float) $data['price'];
                $discount = (float) ($data['discount_value'] ?? 0);
                $type = $data['discount_type'] ?? 'fixed';

                $final = ($type === 'percent')
                    ? $price - ($price * $discount / 100)
                    : $price - $discount;

                $data['final_price'] = round(max($final, 0), 2);
            }

            return $this->repo->updateCourse($course, $data);
        });
    }

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

                ProcessLessonVideo::dispatch($lesson);
                return $lesson;
            }

            return $this->repo->createLesson($lessonData);
        });
    }

    public function addResource($courseId, array $data)
    {
        if (!isset($data['file'])) {
            throw new Exception("File is required");
        }

        $path = $data['file']->store('courses/resources', $this->disk);

        return $this->repo->createResource([
            'course_id' => $courseId,
            'title' => $data['title'],
            'file_path' => $path,
            'file_type' => $data['file']->getClientOriginalExtension()
        ]);
    }

    public function getCourse($id)
    {
        return $this->repo->findCourseWithSyllabus($id);
    }
}
