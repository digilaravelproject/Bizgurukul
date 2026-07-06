<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\AffiliateCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RewardReportController extends Controller
{
    /**
     * Display a comprehensive reward tracking and leaderboard dashboard.
     */
    public function index(\App\Services\AchievementService $achievementService)
    {
        try {
            $now = now();

            // 1. Achievers List (With highest achieved milestone)
            $achievers = User::whereHas('userAchievements', function ($query) {
                    $query->whereIn('user_achievements.status', ['unlocked', 'claimed'])
                        ->whereHas('achievement');
                })
                ->with(['userAchievements' => function ($query) {
                    $query->whereIn('user_achievements.status', ['unlocked', 'claimed'])
                        ->with('achievement') // Fix N+1: properly eager load achievement relation
                        ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                        ->whereNull('achievements.deleted_at')
                        ->select('user_achievements.*')
                        ->orderBy('achievements.target_amount', 'desc');
                }])
                ->withSum('commissions', 'amount')
                ->paginate(10, ['*'], 'achievers_page');

            // 2. Progress Tracker (Users nearing their next milestones)
            $allStudents = User::role('student')
                ->withSum('commissions', 'amount')
                ->get()
                ->map(function ($user) use ($achievementService) {
                    $user->achievement_info = $achievementService->getDashboardData($user);
                    return $user;
                })
                ->filter(function ($user) {
                    return !is_null($user->achievement_info['next_achievement']);
                })
                ->sortBy(function ($user) {
                    return $user->achievement_info['remaining_to_next'];
                });

            // Paginate the collection manually in PHP
            $currentPage = request()->get('progress_page', 1);
            $perPage = 10;
            $currentItems = $allStudents->slice(($currentPage - 1) * $perPage, $perPage)->values();
            
            $progressTracker = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $allStudents->count(),
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                    'pageName' => 'progress_page',
                ]
            );

            // 3. Top Performers (Leaderboard)
            $leaderboard = User::role('student')
                ->where('hide_from_leaderboard', false)
                ->withSum('commissions', 'amount')
                ->addSelect([
                    'earliest_unlock' => UserAchievement::join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                        ->whereNull('achievements.deleted_at')
                        ->select('user_achievements.unlocked_at')
                        ->whereColumn('user_achievements.user_id', 'users.id')
                        ->whereIn('user_achievements.status', ['unlocked', 'claimed'])
                        ->orderBy('user_achievements.unlocked_at', 'asc')
                        ->limit(1),
                    'max_achievement_level' => UserAchievement::join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                        ->whereNull('achievements.deleted_at')
                        ->select('achievements.target_amount')
                        ->whereColumn('user_achievements.user_id', 'users.id')
                        ->whereIn('user_achievements.status', ['unlocked', 'claimed'])
                        ->orderBy('achievements.target_amount', 'desc')
                        ->limit(1)
                ])
                ->orderByRaw('CASE WHEN earliest_unlock IS NULL THEN 1 ELSE 0 END ASC')
                ->orderBy('earliest_unlock', 'asc')
                ->orderBy('id', 'asc')
                ->orderBy('max_achievement_level', 'desc')
                ->take(20)
                ->get();

            // 4. Early Achievers Timeline (Historic sequence)
            $timeline = UserAchievement::whereHas('achievement')
                ->with(['user', 'achievement'])
                ->whereIn('user_achievements.status', ['unlocked', 'claimed'])
                ->whereNotNull('user_achievements.unlocked_at')
                ->orderBy('user_achievements.unlocked_at', 'desc')
                ->paginate(15, ['*'], 'timeline_page');

            return view('admin.rewards.dashboard', compact(
                'achievers',
                'progressTracker',
                'leaderboard',
                'timeline'
            ));
        } catch (\Throwable $th) {
            Log::error("Reward Dashboard Error: " . $th->getMessage(), [
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'trace' => $th->getTraceAsString()
            ]);

            return back()->with('error', 'Ops! Something went wrong while loading the Reward Mastery dashboard. Our technical team has been notified.');
        }
    }
}
