<?php

namespace App\Services;

use App\Repositories\WalletRepository;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    protected WalletRepository $walletRepo;

    public function __construct(WalletRepository $walletRepo)
    {
        $this->walletRepo = $walletRepo;
    }

    public function processCommission(array $data)
    {
        $tdsRate = 2.0;
        $tdsAmount = ($data['amount'] * $tdsRate) / 100;
        $payableAmount = $data['amount'] - $tdsAmount;

        $holdingHours = (int) Setting::get('commission_holding_hours', 24);
        $availableAt = now()->addHours($holdingHours);

        $data['tds_amount'] = $tdsAmount;
        $data['payable_amount'] = $payableAmount;
        $data['available_at'] = $availableAt;
        $data['status'] = $holdingHours > 0 ? 'on_hold' : 'available';

        return $this->walletRepo->createCommission($data);
    }

    public function getWalletDashboardData(int $userId)
    {
        $this->syncAvailableCommissions($userId);

        return [
            'available_balance' => $this->walletRepo->getWithdrawableBalance($userId),
            'on_hold_balance'   => $this->walletRepo->getOnHoldBalance($userId),
            'total_earnings'    => $this->walletRepo->getTotalEarnings($userId),
            'total_withdrawn'   => $this->walletRepo->getTotalWithdrawn($userId),
        ];
    }

    public function syncAvailableCommissions(int $userId = null)
    {
        $query = \App\Models\AffiliateCommission::where('status', 'on_hold')
                    ->where('available_at', '<=', now());
        if ($userId) {
            $query->where('affiliate_id', $userId);
        }
        $query->update(['status' => 'available']);
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
