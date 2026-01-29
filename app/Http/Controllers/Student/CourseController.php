<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        // Published courses fetch karein
        $courses = Course::where('is_published', 1)->latest()->get();
        return view('student.courses.index', compact('courses'));
    }

    public function show($id) // Slug ki jagah ID
    {
        // ID se course find karein
        $course = Course::with('lessons')
            ->where('is_published', 1)
            ->findOrFail($id);

        return view('student.courses.show', compact('course'));
    }
}
