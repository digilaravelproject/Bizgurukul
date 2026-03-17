<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\VideoProgress;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Display a list of completed courses eligible for certificates.
     */
    public function index()
    {
        $user = Auth::user();

        // Fetch completed lesson IDs for calculation
        $completedLessonIds = VideoProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        // Fetch user's enrolled courses (direct and via bundle)
        $unlockedCourseIds = $user->unlockedCourseIds();

        $myCourses = Course::whereIn('id', $unlockedCourseIds)
            ->with('lessons')
            ->withCount('lessons')
            ->get()
            ->map(function ($course) use ($completedLessonIds) {
                // Determine progress
                $completed = $course->lessons->pluck('id')->intersect($completedLessonIds)->count();

                $course->progress_percent = $course->lessons_count > 0
                    ? round(($completed / $course->lessons_count) * 100)
                    : 0;

                // Special case for courses with 0 lessons (e.g. test courses)
                if ($course->lessons_count == 0) {
                     $course->progress_percent = 0; // Fix: 0 instead of 100 to prevent false completion
                }

                return $course;
            });

        return view('student.certificates.index', compact('myCourses'));

    }

    /**
     * Generate/Download the certificate for a specific course.
     */
    public function generate(Course $course)
    {
        $user = Auth::user();

        // 1. Verify User has access to the course
        $hasAccess = in_array($course->id, $user->unlockedCourseIds());

        if (!$hasAccess) {
            return redirect()->back()->with('error', 'You do not have access to this course.');
        }

        // 2. Verify Completion
        $completedLessonIds = VideoProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        $course->load('lessons');
        $completed = $course->lessons->pluck('id')->intersect($completedLessonIds)->count();
        $totalLessons = $course->lessons->count();


        $progress_percent = $totalLessons > 0 ? round(($completed / $totalLessons) * 100) : 0; // Fix: 0 instead of 100 for zero lessons

        if ($progress_percent < 90) {
            return redirect()->back()->with('error', 'You must complete at least 90% of the course to generate a certificate.');
        }

        // 3. Fetch Template
        $templateData = Setting::where('key', 'certificate_template')->first();
        if (!$templateData || empty($templateData->value)) {
            return redirect()->back()->with('error', 'Certificate template has not been set by the Admin.');
        }

        // Just return a view showing the certificate for now.
        // A PDF library can be integrated later to download.
        // Robust path resolution for Base64 conversion
        $cleanedPath = ltrim($templateData->value, '/');
        if (str_starts_with($cleanedPath, 'storage/')) {
            $cleanedPath = substr($cleanedPath, 8);
        }
        $templatePath = storage_path('app/public/' . $cleanedPath);
        $base64Template = '';
        
        if (file_exists($templatePath)) {
            $imageData = file_get_contents($templatePath);
            $mimeType = mime_content_type($templatePath);
            $base64Template = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);
        } else {
            // Fallback to URL with proper storage formatting
            $base64Template = \Illuminate\Support\Facades\Storage::url($templateData->value);
        }

        return view('student.certificates.generate', [
            'course' => $course,
            'user' => $user,
            'templateUrl' => $base64Template
        ]);
    }
}
