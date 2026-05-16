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
    // 1. Display courses purchased by the user (Grouped by Bundle)
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
            // Security check: Must have purchased the course (Admins bypass)
            $user = Auth::user();
            if (!$user->hasRole('Admin') && !$course->isPurchasedBy($user->id)) {
                return redirect()->route('student.my-courses')->with('error', 'You have not purchased this course or it is not available in your plan.');
            }

            // Load lessons with progress filtered by current user
            $course->load(['lessons' => function($q) use ($user) {
                $q->with(['progress' => function($q2) use ($user) {
                    $q2->where('user_id', $user->id);
                }])->orderBy('order_column', 'asc');
            }]);

            $currentLesson = $lesson ?? $course->lessons->first();

            // Explicitly load progress for the current lesson for this user only
            $progress = null;
            if ($currentLesson) {
                $progress = VideoProgress::where('user_id', $user->id)
                    ->where('lesson_id', $currentLesson->id)
                    ->first();
            }

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
            $nextUrl = null;
            $user = Auth::user();

            // if lesson_id is provided we store in VideoProgress table as usual
            if ($request->has('lesson_id')) {
                $request->validate([
                    'lesson_id' => 'required|exists:lessons,id',
                    'seconds' => 'nullable|numeric'
                ]);

                $lesson = Lesson::findOrFail($request->lesson_id);

                // Security check: Must have purchased the course
                if (!$user->hasRole('Admin') && !$lesson->course->isPurchasedBy($user->id)) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized.'], 403);
                }

                // Robust completion check from request
                $isCompletedInput = $request->boolean('completed') || 
                                    $request->boolean('is_completed') || 
                                    $request->input('completed') === 'true' ||
                                    $request->input('is_completed') === 'true';

                // Explicitly find or create to avoid any updateOrCreate quirks
                $progress = VideoProgress::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->first();
                
                if (!$progress) {
                    $progress = new VideoProgress();
                    $progress->user_id = $user->id;
                    $progress->lesson_id = $lesson->id;
                }

                $progress->last_watched_second = (int)$request->input('seconds', 0);
                
                // If either already completed or requested to be completed
                if ($isCompletedInput || $progress->is_completed) {
                    $progress->is_completed = true;
                }

                $progress->save();

                // Safety: Clean up any potential duplicates that might have been created previously
                VideoProgress::where('user_id', $user->id)
                    ->where('lesson_id', $lesson->id)
                    ->where('id', '!=', $progress->id)
                    ->delete();

                Log::info("Progress Saved [User:{$user->id}, Lesson:{$lesson->id}]: Sec={$progress->last_watched_second}, Status=" . ($progress->is_completed ? 'COMPLETED' : 'PENDING'));

                // If completed, find next lesson
                if ($progress->is_completed) {
                    $nextLesson = Lesson::where('course_id', $lesson->course_id)
                        ->where('order_column', '>', $lesson->order_column)
                        ->orderBy('order_column', 'asc')
                        ->first();
                    if ($nextLesson) {
                        $nextUrl = route('student.watch', [$lesson->course_id, $nextLesson->id]);
                    }
                }
            } else {
                // beginner guide or other standalone video progress - keep in session
                $seconds = $request->input('seconds', 0);
                $completed = $request->boolean('completed') || $request->boolean('is_completed');
                $videoId = $request->input('video_id');
                if ($videoId) {
                    $progress = session('beginner_guide.progress', []);
                    $oldCompleted = $progress[$videoId]['completed'] ?? false;
                    $progress[$videoId] = [
                        'seconds' => (int)$seconds, 
                        'completed' => ($completed || $oldCompleted)
                    ];
                    session(['beginner_guide.progress' => $progress]);
                }
            }

            return response()->json([
                'status' => 'saved',
                'next_url' => $nextUrl
            ]);
        } catch (Exception $e) {
            Log::error("Error updating video progress for user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save progress.'], 500);
        }
    }
    // 4. Secure delivery of Video Encryption Key
    public function getVideoKey(Lesson $lesson)
    {
        try {
            // Security check: Must have purchased the course (Admins bypass)
            $user = Auth::user();
            if (!$user) {
                Log::warning("Unauthorized video key request for lesson {$lesson->id}");
                return response('Unauthorized', 401);
            }

            if (!$user->hasRole('Admin') && !$lesson->course->isPurchasedBy($user->id)) {
                return response('Forbidden', 403);
            }

            // Path to the key
            $filename = pathinfo($lesson->video_path, PATHINFO_FILENAME);
            if (!$filename || $filename === 'playlist') {
                // Fallback attempt from directory name if path logic changed
                $filename = basename(dirname($lesson->hls_path));
            }

            $keyPath = "lessons/keys/{$filename}.key";

            if (!\Illuminate\Support\Facades\Storage::disk('local')->exists($keyPath)) {
                Log::error("Video key not found at: [{$keyPath}] for Lesson ID: {$lesson->id}. Check if job stored it correctly.");
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

    /**
     * Display the beginner guide video for students.
     */
    public function beginnerGuide(Request $request)
    {
        $categories = \App\Models\BeginnerGuideCategory::orderBy('order_column')
            ->with(['videos' => function($q) {
                $q->orderBy('order_column');
            }])
            ->get();

        $allVideos = \App\Models\BeginnerGuideVideo::all();
        $progressData = session('beginner_guide.progress', []);

        $selectedId = $request->query('video');
        $selected = null;
        if ($selectedId) {
            $selected = $allVideos->firstWhere('id', $selectedId);
        }
        if (empty($selected)) {
            $selected = $allVideos->sortBy('order_column')->first();
        }

        return view('users.beginner-guide', compact('categories', 'selected', 'progressData'));
    }

    /**
     * Display the resources page with tabs for Product Knowledge, Beginners Guide, and Dynamic Categories.
     */
    public function resources(Request $request)
    {
        $productKnowledge = \App\Models\CourseResource::orderBy('created_at', 'desc')->get();
        $beginnersGuide = \App\Models\BeginnerGuideVideo::with('category_rel')->orderBy('order_column')->get();
        
        // Fetch dynamic categories with their resources
        $resourceCategories = \App\Models\ResourceCategory::active()
            ->with(['resources' => function($q) {
                $q->active();
            }])
            ->orderBy('order_column')
            ->get();

        return view('users.resources', compact('productKnowledge', 'beginnersGuide', 'resourceCategories'));
    }

}
