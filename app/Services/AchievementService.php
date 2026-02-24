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
     * This should be called whenever a user earns commission.
     */
    public function checkAndUnlockAchievements(User $user): array
    {
        $totalEarned = $user->total_earnings;
        $activeAchievements = Achievement::active()->orderBy('target_amount', 'asc')->get();
        $unlockedCount = 0;
        $newlyUnlocked = [];

        foreach ($activeAchievements as $achievement) {
            if ($totalEarned >= $achievement->target_amount) {
                // Check if already exists
                $userAchievement = UserAchievement::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'achievement_id' => $achievement->id,
                    ],
                    [
                        'status' => 'unlocked',
                        'unlocked_at' => now(),
                    ]
                );

                if ($userAchievement->wasRecentlyCreated || ($userAchievement->status === 'locked' && $totalEarned >= $achievement->target_amount)) {
                    if ($userAchievement->status === 'locked') {
                        $userAchievement->update([
                            'status' => 'unlocked',
                            'unlocked_at' => now(),
                        ]);
                    }
                    $unlockedCount++;
                    $newlyUnlocked[] = $achievement;
                }
            } else {
                // Not reached yet, ensure it exists as locked if it doesn't
                UserAchievement::firstOrCreate(
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
        $totalEarned = $user->total_earnings;
        $nextAchievement = $user->next_achievement;

        $currentMilestone = Achievement::active()
            ->where('target_amount', '<=', $totalEarned)
            ->orderBy('target_amount', 'desc')
            ->first();

        $startAmount = $currentMilestone ? $currentMilestone->target_amount : 0;
        $targetAmount = $nextAchievement ? $nextAchievement->target_amount : ($currentMilestone ? $currentMilestone->target_amount * 1.5 : 1000);

        $progressInRange = $totalEarned - $startAmount;
        $totalRange = $targetAmount - $startAmount;
        $percentage = $totalRange > 0 ? min(100, max(0, ($progressInRange / $totalRange) * 100)) : 100;

        // Overall progress percentage (for a general progress bar)
        // If we have a max target, we use that.
        $maxTarget = Achievement::active()->max('target_amount') ?: 1000000;
        $overallPercentage = min(100, ($totalEarned / $maxTarget) * 100);

        return [
            'total_earned' => $totalEarned,
            'next_achievement' => $nextAchievement,
            'current_milestone' => $currentMilestone,
            'percentage' => $percentage,
            'overall_percentage' => $overallPercentage,
            'remaining_to_next' => $nextAchievement ? ($nextAchievement->target_amount - $totalEarned) : 0,
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
            return $userAchievement->update([
                'status' => 'claimed',
                'claimed_at' => now(),
            ]);
        }

        return false;
    }
}
