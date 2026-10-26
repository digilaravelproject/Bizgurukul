<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Services\AchievementService;
use App\Services\OfferRewardService;
use Illuminate\Console\Command;

class RecalculateRewards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rewards:recalculate {--user= : Specific User ID to recalculate} {--force-relock-claimed : Also re-lock claimed achievements if user no longer qualifies}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate student reward milestones and re-lock achievements by strictly excluding offer earnings';

    /**
     * Execute the console command.
     */
    public function handle(AchievementService $achievementService, OfferRewardService $offerRewardService): int
    {
        $this->info('Starting Reward Milestones Recalculation with Offer Exclusion...');

        $specificUserId = $this->option('user');
        $forceRelockClaimed = $this->option('force-relock-claimed');

        $usersQuery = User::query();
        if ($specificUserId) {
            $usersQuery->where('id', $specificUserId);
        } else {
            // Target users who have commissions or achievement records
            $usersQuery->where(function ($q) {
                $q->has('commissions')->orHas('userAchievements');
            });
        }

        $users = $usersQuery->get();
        $this->info("Found {$users->count()} user(s) to evaluate.");

        $relockedCount = 0;
        $unlockedCount = 0;
        $claimedWarningCount = 0;
        $changes = [];

        $activeAchievements = Achievement::active()->orderBy('priority', 'asc')->get();

        foreach ($users as $user) {
            foreach ($activeAchievements as $achievement) {
                $userAchievement = UserAchievement::where('user_id', $user->id)
                    ->where('achievement_id', $achievement->id)
                    ->first();

                $currentStatus = $userAchievement ? $userAchievement->status : 'locked';

                // Eligible earnings excluding offers
                $availableInRange = $user->getAvailableEarningsForRewards($achievement->start_date, $achievement->end_date);
                $excludedOfferEarnings = $offerRewardService->getExcludedEarningsTotalForUser($user, $achievement->start_date, $achievement->end_date);

                if ($availableInRange >= $achievement->target_amount) {
                    if ($currentStatus !== 'claimed' && $currentStatus !== 'unlocked') {
                        UserAchievement::updateOrCreate(
                            [
                                'user_id' => $user->id,
                                'achievement_id' => $achievement->id,
                            ],
                            [
                                'status' => 'unlocked',
                                'unlocked_at' => now(),
                            ]
                        );
                        $unlockedCount++;
                        $changes[] = [
                            $user->id,
                            $user->name,
                            $achievement->short_title ?? $achievement->title,
                            $currentStatus,
                            'unlocked',
                            '₹' . number_format($availableInRange, 2),
                            '₹' . number_format($excludedOfferEarnings, 2),
                        ];
                    }
                } else {
                    // Under target!
                    if ($currentStatus === 'unlocked') {
                        // Relock erroneously unlocked achievement
                        $userAchievement->update(['status' => 'locked']);
                        $relockedCount++;
                        $changes[] = [
                            $user->id,
                            $user->name,
                            $achievement->short_title ?? $achievement->title,
                            'unlocked',
                            'locked (Re-locked)',
                            '₹' . number_format($availableInRange, 2),
                            '₹' . number_format($excludedOfferEarnings, 2),
                        ];
                    } elseif ($currentStatus === 'claimed') {
                        if ($forceRelockClaimed) {
                            $userAchievement->update(['status' => 'locked', 'claimed_at' => null]);
                            $relockedCount++;
                            $changes[] = [
                                $user->id,
                                $user->name,
                                $achievement->short_title ?? $achievement->title,
                                'claimed',
                                'locked (Force Re-locked)',
                                '₹' . number_format($availableInRange, 2),
                                '₹' . number_format($excludedOfferEarnings, 2),
                            ];
                        } else {
                            $claimedWarningCount++;
                            $this->warn("User #{$user->id} ({$user->name}) already CLAIMED '{$achievement->title}', but has only ₹{$availableInRange} eligible earnings (excluded offer: ₹{$excludedOfferEarnings}). Use --force-relock-claimed to revert.");
                        }
                    }
                }
            }
        }

        if (!empty($changes)) {
            $this->newLine();
            $this->table(
                ['User ID', 'Name', 'Milestone', 'Old Status', 'New Status', 'Eligible Earnings', 'Excluded Offer Earnings'],
                $changes
            );
        }

        $this->newLine();
        $this->info("Recalculation Complete!");
        $this->line("- Newly Unlocked: {$unlockedCount}");
        $this->line("- Re-locked (Offer earnings excluded): {$relockedCount}");
        if ($claimedWarningCount > 0) {
            $this->warn("- Already Claimed but under target: {$claimedWarningCount} (Not modified unless --force-relock-claimed is passed)");
        }

        return Command::SUCCESS;
    }
}
