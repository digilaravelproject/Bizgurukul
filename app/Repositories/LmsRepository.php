<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\CourseResource;
use Illuminate\Support\Facades\DB;

class LmsRepository
{
    public function getFilteredCourses(array $filters)
    {
        return Course::query()
            ->with(['category:id,name', 'subCategory:id,name'])
            ->withCount('lessons')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($filters['category_id'] ?? null, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->latest()
            ->paginate(10);
    }

    public function getAllCategories()
    {
        return Category::whereNull('parent_id')
            ->with(['subCategories' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();
    }

    public function getSubCategories($parentId)
    {
        return Category::where('parent_id', $parentId)
            ->where('is_active', true)
            ->get();
    }

    public function createCategory(array $data)
    {
        return Category::create($data);
    }

    public function createCourse(array $data)
    {
        return Course::create($data);
    }

    public function findCourseForUpdate($id)
    {
        return Course::lockForUpdate()->findOrFail($id);
    }

    public function findCourseWithSyllabus($id)
    {
        return Course::with(['category', 'subCategory', 'resources'])
            ->with(['lessons' => fn($q) => $q->orderBy('order_column', 'asc')])
            ->withCount('lessons')
            ->findOrFail($id);
    }

    public function updateCourse(Course $course, array $data)
    {
        $course->update($data);
        return $course;
    }

    public function createLesson(array $data)
    {
        return Lesson::create($data);
    }

    public function getNextOrder($courseId)
    {
        return Lesson::where('course_id', $courseId)->max('order_column') + 1;
    }

    public function createResource(array $data)
    {
        return CourseResource::create($data);
    }

    public function deleteResource($id)
    {
        return CourseResource::findOrFail($id)->delete();
    }
}
