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
            // Check if already unlocked or claimed
            $userAchievement = UserAchievement::where('user_id', $user->id)
                ->where('achievement_id', $achievement->id)
                ->first();

            if ($userAchievement && ($userAchievement->status === 'unlocked' || $userAchievement->status === 'claimed')) {
                continue; // Already processed
            }

            // Calculate earnings in the specific range of this achievement
            $earningsInRange = $user->getEarningsInRange($achievement->start_date, $achievement->end_date);

            if ($earningsInRange >= $achievement->target_amount) {
                // Unlock it
                $userAchievement = UserAchievement::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'achievement_id' => $achievement->id,
                    ],
                    [
                        'status' => 'unlocked',
                        'unlocked_at' => now(),
                    ]
                );

                $newlyUnlocked[] = $achievement;
                
                // The user said: "pehle jiski prioraty hogi woi wala use krna hain then woh achive hojye then next wala"
                // If we want to force sequential achievement, we could break here.
                // However, usually achieving one could immediately make you eligible for the next if targets overlap.
                // But following the user's "next वाला" logic strictly:
                // break; 
            } else {
                // Ensure it exists as locked if it doesn't
                UserAchievement::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'achievement_id' => $achievement->id,
                    ],
                    [
                        'status' => 'locked',
                    ]
                );
                
                // If we follow sequential logic, we should probably stop here because the user
                // should finish this priority before moving to the next.
                break;
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

        // Current earnings for the next achievement's period
        $currentEarningsInRange = $nextAchievement 
            ? $user->getEarningsInRange($nextAchievement->start_date, $nextAchievement->end_date)
            : 0;

        // Current milestone (the one just achieved)
        $currentMilestone = $user->achievements()
            ->wherePivotIn('status', ['unlocked', 'claimed'])
            ->orderBy('priority', 'desc')
            ->orderBy('target_amount', 'desc')
            ->first();

        $startAmount = 0; // In the new logic, each milestone starts from 0 in its period
        $targetAmount = $nextAchievement ? $nextAchievement->target_amount : 1000;

        $progressInRange = $currentEarningsInRange;
        $totalRange = $targetAmount;
        $percentage = $totalRange > 0 ? min(100, max(0, ($progressInRange / $totalRange) * 100)) : 100;

        // Overall progress percentage
        $maxTarget = Achievement::active()->max('target_amount') ?: 1000000;
        $overallPercentage = min(100, ($user->total_earnings / $maxTarget) * 100);

        return [
            'total_earned' => $currentEarningsInRange, // Using range-specific earnings for the display
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
            return $userAchievement->update([
                'status' => 'claimed',
                'claimed_at' => now(),
            ]);
        }

        return false;
    }
}
