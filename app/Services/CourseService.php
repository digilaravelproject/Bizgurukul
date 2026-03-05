<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CourseService
{
    protected $repo;

    protected $mediaService;

    protected $disk;

    public function __construct(CourseRepository $repo, MediaProcessingService $mediaService)
    {
        $this->repo = $repo;
        $this->mediaService = $mediaService;
        $this->disk = config('filesystems.default', 'public');
    }

    public function getFilteredCourses(array $filters)
    {
        return $this->repo->getFilteredCourses($filters);
    }

    public function getCourse($id)
    {
        return $this->repo->findCourse($id);
    }

    public function createCourse(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Handle Images
            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                $data['thumbnail'] = $this->mediaService->compressAndConvertToWebP($data['thumbnail'], 'courses/thumbnails');
            }
            if (isset($data['demo_video']) && $data['demo_video'] instanceof UploadedFile) {
                $data['demo_video_url'] = $data['demo_video']->store('courses/demos', $this->disk);
            }

            // Pricing Logic
            $data = $this->calculatePricing($data);

            return $this->repo->createCourse($data);
        });
    }

    public function updateCourseDetails($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $course = $this->repo->findCourse($id);

            // 1. Handle Thumbnail
            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                // Delete old if exists (logic can be added here or in repo)
                $data['thumbnail'] = $this->mediaService->compressAndConvertToWebP($data['thumbnail'], 'courses/thumbnails');
            } else {
                unset($data['thumbnail']);
            }

            // 2. Handle Demo Video
            if (isset($data['demo_video']) && $data['demo_video'] instanceof UploadedFile) {
                $data['demo_video_url'] = $data['demo_video']->store('courses/demos', $this->disk);
            } else {
                unset($data['demo_video_url']);
            }

            // 3. Price Calculation
            // Merge existing data with new data for calculation if partial update
            $mergedData = array_merge($course->toArray(), $data);
            $calculatedData = $this->calculatePricing($mergedData);

            // Only update fields that are present in $data or calculated
            $updateData = array_merge($data, [
                'final_price' => $calculatedData['final_price'] ?? $course->final_price,
                // Add other calculated fields if necessary
            ]);

            return $this->repo->updateCourse($course, $updateData);
        });
    }

    public function deleteCourse($id)
    {
        return $this->repo->deleteCourse($id);
    }

    // --- Pricing Logic ---
    private function calculatePricing(array $data)
    {
        $price = (float) ($data['website_price'] ?? 0);
        $discountValue = (float) ($data['discount_value'] ?? 0);
        $discountType = $data['discount_type'] ?? 'fixed';

        $finalPrice = $price;

        if ($discountType === 'percent') {
            $finalPrice = $price - ($price * ($discountValue / 100));
        } else {
            $finalPrice = $price - $discountValue;
        }

        $data['final_price'] = max(0, round($finalPrice, 2));

        return $data;
    }

    // --- Lessons ---
    public function addLesson($courseId, array $data)
    {
        return DB::transaction(function () use ($courseId, $data) {
            $lessonData = [
                'course_id' => $courseId,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'order_column' => $this->repo->getNextLessonOrder($courseId),
            ];

            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                $lessonData['thumbnail'] = $this->mediaService->compressAndConvertToWebP($data['thumbnail'], 'lessons/thumbnails');
            }

            if ($data['type'] === 'document' && isset($data['document_file'])) {
                $lessonData['document_path'] = $data['document_file']->store('lessons/docs', $this->disk);
            }

            $lesson = $this->repo->createLesson($lessonData);

            if ($data['type'] === 'video') {
                $lesson->update([
                    'bunny_video_id' => $data['bunny_video_id'] ?? null,
                    'bunny_embed_url' => $data['bunny_embed_url'] ?? null,
                ]);
            }

            return $lesson;

            // End of Add Lesson function
        });
    }

    public function updateLessonMeta($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $lesson = $this->repo->findLesson($id);

            $updateData = ['title' => $data['title']];

            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                // Delete old thumbnail if exists
                if ($lesson->getRawOriginal('thumbnail')) {
                    Storage::disk($this->disk)->delete($lesson->getRawOriginal('thumbnail'));
                }
                $updateData['thumbnail'] = $this->mediaService->compressAndConvertToWebP($data['thumbnail'], 'lessons/thumbnails');
            }

            return $this->repo->updateLesson($lesson, $updateData);
        });
    }

    public function deleteLesson($id)
    {
        return DB::transaction(function () use ($id) {
            $lesson = $this->repo->findLesson($id);

            // 1. Delete Files
            if ($lesson->thumbnail) {
                Storage::disk($this->disk)->delete($lesson->thumbnail);
            }
            if ($lesson->document_path) {
                Storage::disk($this->disk)->delete($lesson->document_path);
            }
            if ($lesson->video_path) {
                Storage::disk($this->disk)->delete($lesson->video_path);
            }
            if ($lesson->hls_path) {
                $hlsFolder = dirname($lesson->hls_path);
                Storage::disk($this->disk)->deleteDirectory($hlsFolder);
            }

            return $this->repo->deleteLesson($id);
        });
    }

    // --- Resources ---
    public function addResource($courseId, array $data)
    {
        if (! isset($data['file'])) {
            throw new Exception('File is required');
        }

        $path = $data['file']->store('courses/resources', $this->disk);

        return $this->repo->createResource([
            'course_id' => $courseId,
            'title' => $data['title'],
            'file_path' => $path,
            'file_type' => $data['file']->getClientOriginalExtension(),
        ]);
    }

    public function deleteResource($id)
    {
        return DB::transaction(function () use ($id) {
            $resource = $this->repo->findResource($id);
            if ($path = $resource->getRawOriginal('file_path')) {
                Storage::disk($this->disk)->delete($path);
            }

            return $this->repo->deleteResource($id);
        });
    }
}
