<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\{User, Course, Bundle, Payment, VideoProgress};
use Exception;

class DashboardController extends Controller
{
    protected $affiliateService;

    public function __construct(\App\Services\AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    public function index()
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login first.');
            }

            // Fetch completed lesson IDs for calculation
            $completedLessonIds = VideoProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->toArray();

            // Fetch and calculate course progress efficiently using withCount
            $myCourses = Payment::where('user_id', $user->id)
                ->where('status', 'success')
                ->whereNotNull('course_id')
                ->with(['course' => function($q) {
                    $q->withCount('lessons')->with('category');
                }])
                ->latest()
                ->get()
                ->pluck('course')
                ->filter()
                ->map(function ($course) use ($completedLessonIds) {
                    $completed = $course->lessons->pluck('id')->intersect($completedLessonIds)->count();

                    $course->progress_percent = $course->lessons_count > 0
                        ? round(($completed / $course->lessons_count) * 100)
                        : 0;
                    $course->completed_lessons = $completed;
                    $course->total_lessons = $course->lessons_count;

                    return $course;
                });

            // Fetch purchased bundles
            $myBundles = Payment::where('user_id', $user->id)
                ->where('status', 'success')
                ->whereNotNull('bundle_id')
                ->with('bundle')
                ->latest()
                ->get()
                ->pluck('bundle')
                ->filter();

            $referralLink = $user->referral_code ? url('/register?ref=' . $user->referral_code) : '';

            $affiliateData = $this->affiliateService->getDashboardData($user);

            return view('student.dashboard', array_merge([
                'user'                => $user,
                'myCourses'           => $myCourses,
                'myBundles'           => $myBundles,
                'referralLink'        => $referralLink,
                'earningsStats'       => $this->affiliateService->getEarningsStats($user),
                'secondaryStats'      => $this->affiliateService->getSecondaryStats($user),
                'graphData'           => $this->affiliateService->getGraphData($user, 7),
                'categoryPerformance' => $this->affiliateService->getCategoryPerformance($user),
            ], $affiliateData));

        } catch (Exception $e) {
            Log::error("Dashboard Error: " . $e->getMessage());
            return abort(500, 'Something went wrong.');
        }
    }
}
