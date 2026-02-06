<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        // Fetch only published courses
        $courses = Course::where('is_published', 1)->latest()->get();
        return view('student.courses.index', compact('courses'));
    }

    public function show($id)
    {
        // Eager load lessons to show curriculum
        $course = Course::with(['lessons' => function ($query) {
            $query->orderBy('order_column', 'asc');
        }])
            ->where('is_published', 1)
            ->findOrFail($id);

        return view('student.courses.show', compact('course'));
    }
}
