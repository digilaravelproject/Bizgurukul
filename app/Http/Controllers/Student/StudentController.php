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

            // 1. Get Unlocked Bundles (Purchased or via Preference Logic)
            $unlockedBundleIds = $user->unlockedBundleIds();
            $myBundles = \App\Models\Bundle::whereIn('id', $unlockedBundleIds)
                ->with(['courses', 'courses.lessons'])
                ->paginate(10, ['*'], 'bundles_page');

            // 2. Get Unlocked Courses (Purchased or via Preference Logic)
            $unlockedCourseIds = $user->unlockedCourseIds();

            // 3. Filter out courses that are already shown inside bundles
            $bundleCourseIds = $myBundles->pluck('courses')->flatten()->pluck('id')->unique();


            $directCourseIds = collect($unlockedCourseIds)->diff($bundleCourseIds);

            $directCourses = Course::whereIn('id', $directCourseIds)->paginate(10, ['*'], 'courses_page');

            return view('users.my-courses', compact('myBundles', 'directCourses', 'unlockedBundleIds', 'unlockedCourseIds'));
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
    // 4. Secure delivery of Video Encryption Key
    public function getVideoKey(Lesson $lesson)
    {
        try {
            // Security check: Must have purchased the course
            if (!$lesson->course->isPurchasedBy(Auth::id())) {
                return response('Unauthorized', 403);
            }

            // Path to the key
            $filename = pathinfo($lesson->video_path, PATHINFO_FILENAME);
            if (!$filename) {
                // Fallback attempt from hls_path
                $filename = basename(dirname($lesson->hls_path));
            }

            $keyPath = "lessons/keys/{$filename}.key";

            if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($keyPath)) {
                Log::error("Video key not found at: {$keyPath} for Lesson ID: {$lesson->id}");
                return response('Key not found', 404);
            }

            $key = \Illuminate\Support\Facades\Storage::disk('local')->get($keyPath);

            return response($key)
                ->header('Content-Type', 'application/octet-stream')
                ->header('Cache-Control', 'no-cache, private');

        } catch (Exception $e) {
            Log::error("Error serving video key for lesson {$lesson->id}: " . $e->getMessage());
            return response('Error', 500);
        }
    }


}
