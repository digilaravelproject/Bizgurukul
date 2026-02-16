<?php

namespace App\Repositories;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\CourseResource;
use Illuminate\Support\Collection;

class CourseRepository
{
    public function getFilteredCourses(array $filters)
    {
        $query = Course::with(['category'])->withCount('lessons');

        if (isset($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['status'])) {
            if ($filters['status'] == 'published') {
                $query->where('is_published', true);
            } elseif ($filters['status'] == 'draft') {
                $query->where('is_published', false);
            }
        }

        return $query->latest()->paginate(10);
    }

    public function findCourse($id): Course
    {
        return Course::with(['lessons', 'resources', 'category', 'subCategory'])->findOrFail($id);
    }

    public function createCourse(array $data): Course
    {
        return Course::create($data);
    }

    public function updateCourse(Course $course, array $data): Course
    {
        $course->update($data);
        return $course;
    }

    public function deleteCourse($id): bool
    {
        return Course::destroy($id);
    }

    // --- Lessons ---
    public function createLesson(array $data): Lesson
    {
        return Lesson::create($data);
    }

    public function findLesson($id): Lesson
    {
        return Lesson::findOrFail($id);
    }

    public function updateLesson(Lesson $lesson, array $data): Lesson
    {
        $lesson->update($data);
        return $lesson;
    }

    public function deleteLesson($id): bool
    {
        return Lesson::destroy($id);
    }

    public function getNextLessonOrder($courseId): int
    {
        return Lesson::where('course_id', $courseId)->max('order_column') + 1;
    }

    // --- Resources ---
    public function createResource(array $data): CourseResource
    {
        return CourseResource::create($data);
    }

    public function findResource($id): CourseResource
    {
        return CourseResource::findOrFail($id);
    }

    public function deleteResource($id): bool
    {
        return CourseResource::destroy($id);
    }
}
