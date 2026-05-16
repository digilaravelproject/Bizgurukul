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
    public function index()
    {
        try {
            $now = now();

            // 1. Achievers List (With highest achieved milestone)
            $achievers = User::whereHas('userAchievements', function ($query) {
                    $query->whereIn('user_achievements.status', ['unlocked', 'claimed'])
                        ->has('achievement'); // Ensure achievement exists and not soft-deleted
                })
                ->with(['userAchievements' => function ($query) {
                    $query->whereIn('user_achievements.status', ['unlocked', 'claimed'])
                        ->has('achievement') // Ensure achievement exists
                        ->with('achievement')
                        ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                        ->whereNull('achievements.deleted_at') // Explicitly handle soft-deleted in join
                        ->select('user_achievements.*')
                        ->orderBy('achievements.target_amount', 'desc');
                }])
                ->withSum('commissions', 'amount')
                ->paginate(10, ['*'], 'achievers_page');

            // 2. Progress Tracker (Users nearing their next milestones)
            $progressTracker = User::role('student')
                ->withSum('commissions', 'amount')
                ->addSelect(['next_milestone_target' => Achievement::select('target_amount')
                    ->whereRaw('target_amount > (SELECT COALESCE(SUM(amount), 0) FROM affiliate_commissions WHERE affiliate_id = users.id)')
                    ->active()
                    ->orderBy('target_amount', 'asc')
                    ->limit(1)
                ])
                ->addSelect(['next_milestone_title' => Achievement::select('short_title')
                    ->whereRaw('target_amount > (SELECT COALESCE(SUM(amount), 0) FROM affiliate_commissions WHERE affiliate_id = users.id)')
                    ->active()
                    ->orderBy('target_amount', 'asc')
                    ->limit(1)
                ])
                ->having('next_milestone_target', '>', 0)
                ->orderByRaw('next_milestone_target - commissions_sum_amount ASC')
                ->paginate(10, ['*'], 'progress_page');

            // 3. Top Performers (Leaderboard)
            $leaderboard = User::role('student')
                ->where('hide_from_leaderboard', false)
                ->withSum('commissions', 'amount')
                ->addSelect([
                    'earliest_unlock' => UserAchievement::select('unlocked_at')
                        ->whereColumn('user_id', 'users.id')
                        ->whereIn('status', ['unlocked', 'claimed'])
                        ->orderBy('unlocked_at', 'asc')
                        ->limit(1),
                    'max_achievement_level' => UserAchievement::join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
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
            $timeline = UserAchievement::has('achievement')->with(['user', 'achievement'])
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
