<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\{User, Course, Bundle, Payment, VideoProgress, Achievement};
use Exception;

class DashboardController extends Controller
{
    protected $affiliateService;
    protected $achievementService;

    public function __construct(\App\Services\AffiliateService $affiliateService, \App\Services\AchievementService $achievementService)
    {
        $this->affiliateService = $affiliateService;
        $this->achievementService = $achievementService;
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

            // Fetch ALL unlocked course IDs (Direct Purchase + From Bundles)
            $unlockedCourseIds = $user->unlockedCourseIds();

            // Fetch course details for these IDs to display progress
            $myCourses = Course::whereIn('id', $unlockedCourseIds)
                ->withCount('lessons')
                ->with('category')
                ->get()
                ->map(function ($course) use ($completedLessonIds) {
                    $courseLessons = $course->lessons->pluck('id');
                    $completed = $courseLessons->intersect($completedLessonIds)->count();

                    $course->progress_percent = $course->lessons_count > 0
                        ? round(($completed / $course->lessons_count) * 100)
                        : 0;
                    $course->completed_lessons = $completed;
                    $course->total_lessons = $course->lessons_count;

                    return $course;
                });

            // Fetch purchased bundles (Directly purchased)
            $myBundles = $user->bundles;

            $referralLink = $user->referral_code ? url('/register?ref=' . $user->referral_code) : '';
            $affiliateData = $this->affiliateService->getDashboardData($user);

            return view('student.dashboard', array_merge([
                'user'                => $user,
                'myCourses'           => $myCourses,
                'myBundles'           => $myBundles,
                'enrolledCount'       => count($unlockedCourseIds) + $myBundles->count(),
                'referralLink'        => $referralLink,
                'achievementData'     => $this->achievementService->getDashboardData($user),
                'earningsStats'       => $this->affiliateService->getEarningsStats($user),
                'secondaryStats'      => $this->affiliateService->getSecondaryStats($user),
                'graphData'           => $this->affiliateService->getGraphData($user, 7),
                'categoryPerformance' => $this->affiliateService->getCategoryPerformance($user),
                'bundleDistribution'  => $this->affiliateService->getBundleDistribution($user),
            ], $affiliateData));

        } catch (Exception $e) {
            Log::error("Dashboard Error: " . $e->getMessage());
            return abort(500, 'Something went wrong.');
        }
    }

    public function rewards()
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            $achievementData = $this->achievementService->getDashboardData($user);

            $allAchievements = Achievement::active()
                ->orderBy('priority', 'asc')
                ->orderBy('target_amount', 'asc')
                ->get();

            $userAchievements = $user->achievements->pluck('pivot.status', 'id')->toArray();
            
            // Calculate progress for each milestone based on its specific dates
            $milestoneProgress = [];
            foreach ($allAchievements as $milestone) {
                $milestoneProgress[$milestone->id] = $user->getEarningsInRange($milestone->start_date, $milestone->end_date);
            }

            return view('student.rewards', [
                'user'              => $user,
                'achievementData'   => $achievementData,
                'allMilestones'     => $allAchievements,
                'userMilestones'    => $userAchievements,
                'milestoneProgress' => $milestoneProgress,
                'earningsStats'     => $this->affiliateService->getEarningsStats($user),
            ]);

        } catch (Exception $e) {
            Log::error("Rewards Page Error: " . $e->getMessage());
            return abort(500, 'Something went wrong.');
        }
    }

    public function claimReward($id)
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            $success = $this->achievementService->claimReward($user, (int)$id);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Reward claimed successfully!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Unable to claim reward. Reach the milestone first!'
            ], 400);

        } catch (Exception $e) {
            Log::error("Claim Reward Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.'
            ], 500);
        }
    }
}
