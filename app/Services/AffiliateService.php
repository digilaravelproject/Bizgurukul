<?php

namespace App\Services;

use App\Repositories\AffiliateRepository;
use App\Models\AffiliateCommission;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffiliateService
{
    protected $affiliateRepo;

    public function __construct(AffiliateRepository $affiliateRepo)
    {
        $this->affiliateRepo = $affiliateRepo;
    }

    public function getHistory()
    {
        return $this->affiliateRepo->getPaginatedCommissions();
    }

    public function processPayout($commissionId)
    {
        return DB::transaction(function () use ($commissionId) {
            try {
                // LOCK the row to prevent double payment
                $commission = AffiliateCommission::lockForUpdate()->find($commissionId);

                if (!$commission) {
                    throw new Exception("Commission not found.");
                }

                if ($commission->status !== 'pending') {
                    throw new Exception("Commission is already processed.");
                }

                $user = $commission->affiliate;
                $amount = $commission->amount;

                // 1. Get Current Balance from User (Wallet Logic)
                // We assume user model has correct accessor or we should calc from transactions?
                // For now, relying on User's accessor as per previous logic.
                $currentBalance = $user->wallet_balance;
                $newBalance = $currentBalance + $amount;

                // 2. Create Wallet Transaction
                $this->affiliateRepo->createWalletTransaction([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'type' => 'credit',
                    'balance_after' => $newBalance,
                    'description' => "Commission for referral #{$commission->id}",
                    'reference_id' => $commission->id,
                    'reference_type' => AffiliateCommission::class,
                ]);

                // 3. Update Commission Status
                $commission->status = 'paid';
                $this->affiliateRepo->saveCommission($commission);

                return true;

            } catch (Exception $e) {
                Log::error("AffiliateService Error [processPayout]: " . $e->getMessage());
                throw $e;
            }
        });
    }
}
