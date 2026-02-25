<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Payment;
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

        // Fetch user's enrolled courses that are successful
        $myCourses = Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->whereNotNull('course_id')
            ->with(['course' => function($q) {
                $q->withCount('lessons');
            }])
            ->get()
            ->pluck('course')
            ->filter()
            ->map(function ($course) use ($completedLessonIds) {
                // Determine progress
                $completed = $course->lessons->pluck('id')->intersect($completedLessonIds)->count();

                $course->progress_percent = $course->lessons_count > 0
                    ? round(($completed / $course->lessons_count) * 100)
                    : 0;

                // Special case for courses with 0 lessons (e.g. test courses)
                if ($course->lessons_count == 0) {
                     $course->progress_percent = 100;
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
        $hasAccess = Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->where('course_id', $course->id)
            ->exists();

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

        $progress_percent = $totalLessons > 0 ? round(($completed / $totalLessons) * 100) : 100;

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
        $templateUrl = \Illuminate\Support\Facades\Storage::url($templateData->value);

        return view('student.certificates.generate', [
            'course' => $course,
            'user' => $user,
            'templateUrl' => $templateUrl
        ]);
    }
}
