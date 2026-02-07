<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    // 1. User ke kharide hue courses dikhana
    public function myCourses()
    {
        $courses = Course::whereHas('payments', function ($q) {
            $q->where('user_id', Auth::id())->where('status', 'success');
        })->get();

        return view('users.my-courses', compact('courses'));
    }

    // 2. Course player/watch page
    public function watch(Course $course, Lesson $lesson = null)
    {
        // Security check
        if (!$course->isPurchasedBy(Auth::id())) {
            return redirect()->route('student.courses.show', $course->id)->with('error', 'Please purchase this course.');
        }

        $course->load('lessons');
        $currentLesson = $lesson ?? $course->lessons->first();

        // Load progress
        $progress = VideoProgress::where('user_id', Auth::id())
            ->where('lesson_id', $currentLesson->id)
            ->first();

        return view('users.watch', compact('course', 'currentLesson', 'progress'));
    }

    // 3. API for saving progress via JS
    public function updateProgress(Request $request)
    {
        VideoProgress::updateOrCreate(
            ['user_id' => Auth::id(), 'lesson_id' => $request->lesson_id],
            ['last_watched_second' => $request->seconds, 'is_completed' => $request->completed ?? false]
        );
        return response()->json(['status' => 'saved']);
    }
}
