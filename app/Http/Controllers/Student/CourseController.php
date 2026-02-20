<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class CourseController extends Controller
{
    public function index()
    {
        try {
            // Fetch only published courses
            $courses = Course::where('is_published', 1)->latest()->paginate(15);
            return view('student.courses.index', compact('courses'));
        } catch (Exception $e) {
            Log::error("Error loading courses index: " . $e->getMessage());
            return back()->with('error', 'Unable to load courses at this time.');
        }
    }

    public function show($id)
    {
        try {
            // Eager load lessons to show curriculum
            $course = Course::with(['lessons' => function ($query) {
                $query->orderBy('order_column', 'asc');
            }])
                ->where('is_published', 1)
                ->findOrFail($id);

            return view('student.courses.show', compact('course'));
        } catch (Exception $e) {
            Log::error("Error loading course details for ID {$id}: " . $e->getMessage());
            return redirect()->route('student.courses.index')->with('error', 'Course not found or an error occurred.');
        }
    }
}
