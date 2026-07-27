<?php

namespace App\Services;

use App\Models\User;
use App\Models\Bundle;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\WalletService;
use App\Services\CommissionCalculatorService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class BundleUpgradeService
{
    protected PaymentService $paymentService;
    protected WalletService $walletService;
    protected CommissionCalculatorService $commissionCalculator;

    public function __construct(
        PaymentService $paymentService,
        WalletService $walletService,
        CommissionCalculatorService $commissionCalculator
    ) {
        $this->paymentService = $paymentService;
        $this->walletService = $walletService;
        $this->commissionCalculator = $commissionCalculator;
    }

    /**
     * Calculate Differential Balance (Target Price - Already Paid)
     */
    public function getDifferentialAmount(User $user, Bundle $targetBundle, float $alreadyPaidAmount): float
    {
        $targetPrice = $user->referred_by ? $targetBundle->affiliate_price : $targetBundle->website_price;
        return max(0, $targetPrice - $alreadyPaidAmount);
    }

    /**
     * Initiate Differential Payment Session (Online Gateway Checkout)
     */
    public function initiateUpgradePayment(User $user, Bundle $targetBundle, float $alreadyPaidAmount = 2999.00)
    {
        $differentialAmount = $this->getDifferentialAmount($user, $targetBundle, $alreadyPaidAmount);

        if ($differentialAmount <= 0) {
            throw new Exception("No balance due for upgrade.");
        }

        return $this->paymentService->initiatePayment($user, $targetBundle, $differentialAmount);
    }

    /**
     * Complete Upgrade upon Online Payment Verification
     */
    public function completeUpgrade(array $paymentVerificationData, Bundle $targetBundle)
    {
        return DB::transaction(function () use ($paymentVerificationData, $targetBundle) {
            $payment = $this->paymentService->verifyPayment($paymentVerificationData);
            $paymentLocked = Payment::where('id', $payment->id)->lockForUpdate()->first();

            if ($paymentLocked->is_upgrade_processed) {
                Log::warning("Upgrade payment already processed for payment ID: {$paymentLocked->id}");
                return $paymentLocked;
            }

            $user = User::where('id', $paymentLocked->user_id)->lockForUpdate()->first();

            // Grant Bundle Access by setting payment bundle_id and paymentable
            $paymentLocked->bundle_id = $targetBundle->id;
            $paymentLocked->paymentable_type = get_class($targetBundle);
            $paymentLocked->paymentable_id = $targetBundle->id;
            $paymentLocked->is_upgrade_processed = true;
            $paymentLocked->save();

            // Disburse Sponsor Commission
            $this->disburseSponsorCommission($user, $targetBundle);

            return $paymentLocked;
        });
    }

    /**
     * Manual Admin Resolution (For direct/offline differential settlements)
     */
    public function resolveManualAdminUpgrade(User $user, Bundle $targetBundle, float $alreadyPaidAmount)
    {
        return DB::transaction(function () use ($user, $targetBundle, $alreadyPaidAmount) {
            $userLocked = User::where('id', $user->id)->lockForUpdate()->first();

            // Find existing success payment for this user or update the payment record
            $payment = Payment::where('user_id', $userLocked->id)->where('status', 'success')->latest()->first();

            if ($payment) {
                $payment->bundle_id = $targetBundle->id;
                $payment->paymentable_type = get_class($targetBundle);
                $payment->paymentable_id = $targetBundle->id;
                $payment->save();
            }

            // Disburse Sponsor Commission
            $this->disburseSponsorCommission($userLocked, $targetBundle);

            Log::info("Manual Admin Upgrade Executed", [
                'user_id' => $userLocked->id,
                'target_bundle_id' => $targetBundle->id,
                'already_paid' => $alreadyPaidAmount,
            ]);

            return true;
        });
    }

    /**
     * Centralized Safe Commission Disbursement with Upgrade Delta Calculation
     */
    protected function disburseSponsorCommission(User $user, Bundle $bundle)
    {
        if (!$user->referred_by) {
            return;
        }

        $sponsor = User::where('id', $user->referred_by)->lockForUpdate()->first();
        if (!$sponsor) {
            return;
        }

        // Full commission for the target upgraded bundle
        $targetCommission = $this->commissionCalculator->calculateCommission($sponsor, $bundle);

        // Check if sponsor has already received any commission for this referred user in the past
        $alreadyCreditedCommission = \App\Models\AffiliateCommission::where('affiliate_id', $sponsor->id)
            ->where('referred_user_id', $user->id)
            ->sum('amount');

        // Payable differential commission = Target Bundle Commission - Already Credited Commission
        $payableCommission = max(0, $targetCommission - $alreadyCreditedCommission);

        if ($payableCommission > 0) {
            $this->walletService->processCommission([
                'affiliate_id' => $sponsor->id,
                'referred_user_id' => $user->id,
                'amount' => $payableCommission,
                'reference_id' => $bundle->id,
                'reference_type' => get_class($bundle),
                'notes' => "Commission for Bundle Upgrade: {$bundle->title} (User: {$user->email})",
            ]);

            Log::info("Sponsor Wallet Credited for Upgrade", [
                'sponsor_id' => $sponsor->id,
                'user_id' => $user->id,
                'target_commission' => $targetCommission,
                'already_credited' => $alreadyCreditedCommission,
                'payable_commission' => $payableCommission,
                'bundle_id' => $bundle->id,
            ]);
        }
    }
}
