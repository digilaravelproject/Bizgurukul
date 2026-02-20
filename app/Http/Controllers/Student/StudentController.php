<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class StudentController extends Controller
{
    // 1. User ke kharide hue courses dikhana (Grouped by Bundle)
    public function myCourses()
    {
        try {
            $user = Auth::user();

            // 1. Get Purchased Bundles
            $purchasedBundleIds = \App\Models\Payment::where('user_id', $user->id)
                ->where('status', 'success')
                ->whereNotNull('bundle_id')
                ->pluck('bundle_id');

            $myBundles = \App\Models\Bundle::whereIn('id', $purchasedBundleIds)
                ->with(['courses', 'courses.lessons']) // Eager load lessons for progress count if needed later
                ->paginate(10, ['*'], 'bundles_page');

            // 2. Get Direct Purchased Course IDs
            $purchasedCourseIds = \App\Models\Payment::where('user_id', $user->id)
                ->where('status', 'success')
                ->whereNotNull('course_id')
                ->pluck('course_id');

            // 3. Filter out courses that are already shown inside bundles
            // Collecting all course IDs present in purchased bundles
            $bundleCourseIds = $myBundles->pluck('courses')->flatten()->pluck('id')->unique();

            // Courses that are bought directly AND NOT part of any purchased bundle
            $directCourseIds = $purchasedCourseIds->diff($bundleCourseIds);

            $directCourses = Course::whereIn('id', $directCourseIds)->paginate(10, ['*'], 'courses_page');

            return view('users.my-courses', compact('myBundles', 'directCourses'));
        } catch (Exception $e) {
            Log::error("Error loading my courses for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Something went wrong while loading your courses.');
        }
    }

    // 2. Course player/watch page
    public function watch(Course $course, ?Lesson $lesson = null)
    {
        try {
            // Security check
            if (!$course->isPurchasedBy(Auth::id())) {
                return redirect()->route('student.courses.show', $course->id)->with('error', 'Please purchase this course.');
            }

            $course->load(['lessons' => function($q) {
                $q->with(['progress' => function($q2) {
                    $q2->where('user_id', Auth::id());
                }]);
            }]);

            $currentLesson = $lesson ?? $course->lessons->first();

            // Load progress for current lesson
            $progress = $currentLesson ? $currentLesson->progress : null;

            return view('users.watch', compact('course', 'currentLesson', 'progress'));

        } catch (Exception $e) {
            Log::error("Error loading watch page for course {$course->id} (User: " . Auth::id() . "): " . $e->getMessage());
            return redirect()->route('student.my-courses')->with('error', 'An error occurred while loading the video player.');
        }
    }

    // 3. API for saving progress via JS
    public function updateProgress(Request $request)
    {
        try {
            $request->validate([
                'lesson_id' => 'required|exists:lessons,id',
                'seconds' => 'required|numeric'
            ]);

            VideoProgress::updateOrCreate(
                ['user_id' => Auth::id(), 'lesson_id' => $request->lesson_id],
                ['last_watched_second' => $request->seconds, 'is_completed' => $request->completed ?? false]
            );
            return response()->json(['status' => 'saved']);
        } catch (Exception $e) {
            Log::error("Error updating video progress for user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save progress.'], 500);
        }
    }
}
