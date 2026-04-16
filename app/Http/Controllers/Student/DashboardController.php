<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\{User, Course, Bundle, Payment, VideoProgress, Achievement};
use Carbon\Carbon;
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

            // Fetch ALL unlocked course IDs and calculate progress
            $unlockedCourseIds = $user->unlockedCourseIds();
            $myCourses = $this->getCourseProgress($user, $unlockedCourseIds);

            // Fetch purchased bundles
            $myBundles = $user->bundles;
            $affiliateData = $this->affiliateService->getDashboardData($user);

            // Fetch Survey Questions if not completed
            $surveyQuestions = [];
            if (!$user->survey_completed) {
                $surveyQuestions = \App\Models\SurveyQuestion::where('is_active', true)
                    ->with('options')
                    ->get();
            }

            return view('student.dashboard', array_merge([
                'user'                => $user,
                'myCourses'           => $myCourses,
                'myBundles'           => $myBundles,
                'surveyQuestions'     => $surveyQuestions,
                'enrolledCount'       => count($unlockedCourseIds) + $myBundles->count(),
                'referralLink'        => $user->referral_code ? url('/register?ref=' . $user->referral_code) : '',
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

    /**
     * Calculate progress for each unlocked course
     */
    protected function getCourseProgress(User $user, array $courseIds)
    {
        $completedLessonIds = VideoProgress::where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('lesson_id')
            ->toArray();

        return Course::whereIn('id', $courseIds)
            ->withCount('lessons')
            ->with('category', 'lessons:id')
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

    /**
     * Fetch refined graph data for dashboard via AJAX
     */
    public function getChartData(\Illuminate\Http\Request $request)
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $range = $request->query('range', '7');

            // Map range to days
            $days = match($range) {
                'week'    => 7,
                'month'   => 30,
                '6month'  => 180,
                'lifetime' => 0, // 0 handles lifetime logic in getGraphData
                default   => 7
            };

            $data = $this->affiliateService->getGraphData($user, (int)$days);

            return response()->json($data);

        } catch (Exception $e) {
            Log::error("Chart Data Error: " . $e->getMessage());
            return response()->json(['error' => 'Failed to load data'], 500);
        }
    }
}
