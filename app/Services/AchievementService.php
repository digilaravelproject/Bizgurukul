<?php

namespace App\Services;

use App\Models\User;
use App\Models\Achievement;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AchievementService
{
    /**
     * Check and unlock new achievements for a user.
     * This should be called whenever a user earns commission or claims a reward.
     */
    public function checkAndUnlockAchievements(User $user): array
    {
        $now = now();
        
        // Get active achievements within their date range, ordered by priority
        $activeAchievements = Achievement::active()
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
            })
            ->orderBy('priority', 'asc')
            ->orderBy('target_amount', 'asc')
            ->get();

        $newlyUnlocked = [];

        foreach ($activeAchievements as $achievement) {
            // Check if already claimed
            $userAchievement = UserAchievement::where('user_id', $user->id)
                ->where('achievement_id', $achievement->id)
                ->first();

            if ($userAchievement && $userAchievement->status === 'claimed') {
                continue; // Already claimed/consumed
            }

            // Calculate usable available earnings in the specific range (Total Earned - Claimed Rewards)
            $availableInRange = $user->getAvailableEarningsForRewards($achievement->start_date, $achievement->end_date);

            if ($availableInRange >= $achievement->target_amount) {
                // Unlock it if not already unlocked
                $isNewlyUnlocked = !$userAchievement || $userAchievement->status !== 'unlocked';

                $userAchievement = UserAchievement::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'achievement_id' => $achievement->id,
                    ],
                    [
                        'status' => 'unlocked',
                        'unlocked_at' => $userAchievement && $userAchievement->unlocked_at ? $userAchievement->unlocked_at : now(),
                    ]
                );

                if ($isNewlyUnlocked) {
                    $newlyUnlocked[] = $achievement;
                }
            } else {
                // If previously unlocked but balance consumed by another claim, re-lock it
                UserAchievement::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'achievement_id' => $achievement->id,
                    ],
                    [
                        'status' => 'locked',
                    ]
                );
            }
        }

        return $newlyUnlocked;
    }

    /**
     * Get data for the speedometer dashboard.
     */
    public function getDashboardData(User $user): array
    {
        $now = now();

        // Get the next achievement the user is working on
        // That is: the one with lowest priority that is active and locked for this user
        $nextAchievement = Achievement::active()
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
            })
            ->whereNotExists(function ($query) use ($user) {
                $query->select(DB::raw(1))
                      ->from('user_achievements')
                      ->whereColumn('user_achievements.achievement_id', 'achievements.id')
                      ->where('user_achievements.user_id', $user->id)
                      ->whereIn('user_achievements.status', ['unlocked', 'claimed']);
            })
            ->orderBy('priority', 'asc')
            ->orderBy('target_amount', 'asc')
            ->first();

        // If no specifically active achievement found, look for ANY active achievement to show progress
        if (!$nextAchievement) {
            $nextAchievement = Achievement::active()
            ->where(function ($query) use ($now) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', $now);
            })
            ->orderBy('priority', 'asc')
            ->orderBy('target_amount', 'asc')
            ->first();
        }

        // Available earnings for the next achievement's period after claimed deductions
        $currentEarningsInRange = $nextAchievement 
            ? $user->getAvailableEarningsForRewards($nextAchievement->start_date, $nextAchievement->end_date)
            : 0;

        // Current milestone (the highest achieved/claimed one)
        $currentMilestone = $user->achievements()
            ->wherePivotIn('status', ['unlocked', 'claimed'])
            ->orderBy('priority', 'desc')
            ->orderBy('target_amount', 'desc')
            ->first();

        $targetAmount = $nextAchievement ? $nextAchievement->target_amount : 1000;

        $progressInRange = $currentEarningsInRange;
        $totalRange = $targetAmount;
        $percentage = $totalRange > 0 ? min(100, max(0, ($progressInRange / $totalRange) * 100)) : 100;

        // Overall progress percentage
        $maxTarget = Achievement::active()->max('target_amount') ?: 1000000;
        $overallPercentage = min(100, ($user->total_earnings / $maxTarget) * 100);

        return [
            'total_earned' => $currentEarningsInRange,
            'total_commission' => $user->total_earnings,
            'next_achievement' => $nextAchievement,
            'current_milestone' => $currentMilestone,
            'percentage' => $percentage,
            'overall_percentage' => $overallPercentage,
            'remaining_to_next' => $nextAchievement ? max(0, $nextAchievement->target_amount - $currentEarningsInRange) : 0,
            'is_date_based' => $nextAchievement && ($nextAchievement->start_date || $nextAchievement->end_date),
            'start_date' => $nextAchievement ? $nextAchievement->start_date : null,
            'end_date' => $nextAchievement ? $nextAchievement->end_date : null,
        ];
    }

    /**
     * Claim a reward for an unlocked achievement.
     */
    public function claimReward(User $user, int $achievementId): bool
    {
        $userAchievement = UserAchievement::where('user_id', $user->id)
            ->where('achievement_id', $achievementId)
            ->where('status', 'unlocked')
            ->first();

        if ($userAchievement) {
            $updated = $userAchievement->update([
                'status' => 'claimed',
                'claimed_at' => now(),
            ]);

            if ($updated) {
                // Re-evaluate and sync remaining achievements since available balance has been consumed
                $this->checkAndUnlockAchievements($user);
                return true;
            }
        }

        return false;
    }
}
