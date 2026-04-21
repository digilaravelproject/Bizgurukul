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
            // 1. Achievers List (With highest achieved milestone)
            $achievers = User::whereHas('userAchievements', function ($query) {
                    $query->whereIn('status', ['unlocked', 'claimed']);
                })
                ->with(['userAchievements' => function ($query) {
                    $query->whereIn('status', ['unlocked', 'claimed'])
                        ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                        ->orderBy('achievements.target_amount', 'desc');
                }])
                ->withSum('commissions', 'amount')
                ->paginate(10, ['*'], 'achievers_page');

            // 2. Progress Tracker (Users nearing their next milestones)
            // Optimized with individual total earnings and subquery for next achievement
            $progressTracker = User::role('student') // assuming students track rewards
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
                ->orderByRaw('next_milestone_target - commissions_sum_amount ASC') // Closest to next milestone first
                ->paginate(10, ['*'], 'progress_page');

            // 3. Top Performers (Leaderboard)
            $leaderboard = User::role('student')
                ->where('hide_from_leaderboard', false)
                ->withSum('commissions', 'amount')
                ->orderByDesc('commissions_sum_amount')
                ->take(20)
                ->get();

            // 4. Early Achievers Timeline (Historic sequence)
            $timeline = UserAchievement::with(['user', 'achievement'])
                ->whereIn('status', ['unlocked', 'claimed'])
                ->whereNotNull('unlocked_at')
                ->orderBy('unlocked_at', 'desc')
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
