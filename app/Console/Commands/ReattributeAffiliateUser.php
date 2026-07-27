<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Bundle;
use App\Services\BundleUpgradeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReattributeAffiliateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate:reattribute 
                            {--user= : Referred user email} 
                            {--sponsor= : Sponsor email} 
                            {--target-bundle= : Target Bundle ID (Optional)} 
                            {--paid-amount= : Already paid amount (Optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dynamically re-attribute any referred user under any sponsor, with optional bundle upgrade resolution.';

    /**
     * Execute the console command.
     */
    public function handle(BundleUpgradeService $upgradeService)
    {
        $userEmail = $this->option('user') ?: $this->ask('Enter Referred User Email');
        $sponsorEmail = $this->option('sponsor') ?: $this->ask('Enter Sponsor Email');

        $user = User::where('email', $userEmail)->first();
        $sponsor = User::where('email', $sponsorEmail)->first();

        if (!$user) {
            $this->error("User with email [{$userEmail}] not found.");
            return 1;
        }

        if (!$sponsor) {
            $this->error("Sponsor with email [{$sponsorEmail}] not found.");
            return 1;
        }

        DB::transaction(function () use ($user, $sponsor) {
            $user->referred_by = $sponsor->id;
            $user->save();

            Log::info("Affiliate Re-attribution Executed", [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'sponsor_id' => $sponsor->id,
                'sponsor_email' => $sponsor->email,
            ]);
        });

        $this->info("✓ Successfully linked User [{$user->email}] to Sponsor [{$sponsor->email}] (ID: {$sponsor->id}).");

        // Optional Automated Upgrade Resolution if target bundle is specified
        $targetBundleId = $this->option('target-bundle');
        if ($targetBundleId) {
            $bundle = Bundle::find($targetBundleId);
            if ($bundle) {
                $paidAmount = (float) ($this->option('paid-amount') ?: 0);
                $this->info("Resolving manual upgrade to Bundle: {$bundle->title} (Target Bundle ID: {$bundle->id})...");

                $upgradeService->resolveManualAdminUpgrade($user, $bundle, $paidAmount);
                $this->info("✓ Manual Bundle Upgrade & Sponsor Commission Disbursed Successfully!");
            } else {
                $this->error("Target Bundle ID [{$targetBundleId}] not found.");
            }
        }

        return 0;
    }
}
