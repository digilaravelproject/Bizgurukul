<?php

namespace App\Services;

use App\Repositories\WalletRepository;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    protected WalletRepository $walletRepo;
    protected AchievementService $achievementService;

    public function __construct(WalletRepository $walletRepo, AchievementService $achievementService)
    {
        $this->walletRepo = $walletRepo;
        $this->achievementService = $achievementService;
    }

    public function processCommission(array $data)
    {
        $tdsEnabled = (bool) Setting::get('tds_enabled', true);
        $tdsRate = $tdsEnabled ? 2.0 : 0.0;
        $tdsAmount = ($data['amount'] * $tdsRate) / 100;
        $payableAmount = $data['amount'] - $tdsAmount;

        $holdingHours = (int) Setting::get('commission_holding_hours', 24);
        $availableAt = now()->addHours($holdingHours);

        $data['tds_amount'] = $tdsAmount;
        $data['payable_amount'] = $payableAmount;
        $data['available_at'] = $availableAt;
        $data['status'] = $holdingHours > 0 ? 'on_hold' : 'available';

        $commission = $this->walletRepo->createCommission($data);

        // Trigger Achievement Check
        /** @var \App\Models\User $user */
        $user = \App\Models\User::find($data['affiliate_id']);
        if ($user) {
            $this->achievementService->checkAndUnlockAchievements($user);
        }

        return $commission;
    }

    public function getWalletDashboardData(int $userId)
    {
        $this->syncAvailableCommissions($userId);

        return [
            'available_balance'     => $this->walletRepo->getWithdrawableBalance($userId), // Gross for summary
            'available_balance_net' => $this->walletRepo->getWithdrawableBalanceNet($userId), // Net for payout
            'on_hold_balance'       => $this->walletRepo->getOnHoldBalance($userId), // Gross for summary
            'on_hold_balance_net'   => $this->walletRepo->getOnHoldBalanceNet($userId), // Net
            'pending_balance'       => $this->walletRepo->getPendingBalance($userId), // Gross for summary
            'total_earnings'        => $this->walletRepo->getTotalEarnings($userId),
            'total_withdrawn'       => $this->walletRepo->getTotalWithdrawn($userId),
            'total_tds'             => $this->walletRepo->getTotalTdsDeducted($userId),
            'tds_enabled'           => (bool) Setting::get('tds_enabled', true),
        ];
    }

    public function syncAvailableCommissions(?int $userId = null)
    {
        // 1. Standard holding period sync: On-Hold -> Available
        $query = \App\Models\AffiliateCommission::where('status', 'on_hold')
                    ->where('available_at', '<=', now());
        if ($userId) {
            $query->where('affiliate_id', $userId);
        }
        $query->update(['status' => 'available']);

        // 2. DATA INTEGRITY HEALER (Root Cause Fix): 
        // If a commission is NOT 'paid', 'requested', or 'processing', 
        // it should NOT have a withdrawal_request_id. If it does, it's orphan data and should be cleared.
        $integrityQuery = \App\Models\AffiliateCommission::whereNotIn('status', ['paid', 'requested', 'processing'])
                            ->whereNotNull('withdrawal_request_id');
        if ($userId) {
            $integrityQuery->where('affiliate_id', $userId);
        }
        $integrityQuery->update(['withdrawal_request_id' => null]);
    }

    /**
     * Recalculate 'available_at' for all On-Hold commissions when admin settings change.
     */
    public function recalculateHoldPeriod()
    {
        $hours = (int) Setting::get('commission_holding_hours', 24);
        $commissions = \App\Models\AffiliateCommission::where('status', 'on_hold')->get();

        foreach ($commissions as $comm) {
            $comm->update([
                'available_at' => $comm->created_at->addHours($hours)
            ]);
        }
    }

    public function manuallyApproveCommission($commissionId)
    {
        $commission = $this->walletRepo->getCommissionById($commissionId);
        if ($commission->status === 'on_hold') {
            $commission->update([
                'status' => 'available',
                'available_at' => now()->subMinute(),
            ]);
        }
        return $commission;
    }

    public function requestWithdrawal(int $userId, array $commissionIds)
    {
        DB::beginTransaction();
        try {
            $this->syncAvailableCommissions($userId);

            $commissions = $this->walletRepo->getAvailableCommissionsByIds($commissionIds, $userId);

            if ($commissions->isEmpty()) {
                throw new Exception('No valid available commissions selected for withdrawal.');
            }

            $totalAmount = $commissions->sum('amount');
            $totalTds = $commissions->sum('tds_amount');
            $totalPayable = $commissions->sum('payable_amount');

            $withdrawal = $this->walletRepo->createWithdrawalRequest([
                'user_id' => $userId,
                'amount' => $totalAmount,
                'tds_deducted' => $totalTds,
                'payable_amount' => $totalPayable,
                'status' => 'pending',
            ]);

            $this->walletRepo->updateCommissions($commissions->pluck('id')->toArray(), [
                'withdrawal_request_id' => $withdrawal->id,
                'status' => 'requested'
            ]);

            DB::commit();
            return $withdrawal;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function approveWithdrawal($withdrawalId, array $adminDetails)
    {
        DB::beginTransaction();
        try {
            $withdrawal = \App\Models\WithdrawalRequest::where('id', $withdrawalId)->lockForUpdate()->firstOrFail();

            if ($withdrawal->status !== 'pending' && $withdrawal->status !== 'processing') {
                throw new Exception("Withdrawal is already {$withdrawal->status}.");
            }

            $withdrawal->update([
                'status' => 'approved',
                'transaction_id' => $adminDetails['transaction_id'] ?? null,
                'reference_number' => $adminDetails['reference_number'] ?? null,
                'payment_method' => $adminDetails['payment_method'] ?? null,
                'admin_note' => $adminDetails['admin_note'] ?? null,
            ]);

            $this->walletRepo->updateCommissions($withdrawal->commissions->pluck('id')->toArray(), [
                'status' => 'paid',
                'processed_at' => now(),
            ]);

            DB::commit();
            return $withdrawal;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rejectWithdrawal($withdrawalId, $adminNote)
    {
        DB::beginTransaction();
        try {
            $withdrawal = \App\Models\WithdrawalRequest::where('id', $withdrawalId)->lockForUpdate()->firstOrFail();

            if ($withdrawal->status !== 'pending' && $withdrawal->status !== 'processing') {
                throw new Exception("Withdrawal cannot be rejected because it is {$withdrawal->status}.");
            }

            $withdrawal->update([
                'status' => 'rejected',
                'admin_note' => $adminNote,
            ]);

            $this->walletRepo->updateCommissions($withdrawal->commissions->pluck('id')->toArray(), [
                'status' => 'available',
                'withdrawal_request_id' => null,
            ]);

            DB::commit();
            return $withdrawal;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
