<?php

namespace App\Repositories;

use App\Models\AffiliateCommission;
use App\Models\WithdrawalRequest;
use Illuminate\Support\Facades\DB;

class WalletRepository
{
    public function createCommission(array $data)
    {
        return AffiliateCommission::create($data);
    }

    public function getCommissionById($id)
    {
        return AffiliateCommission::findOrFail($id);
    }

    public function getWithdrawableBalance(int $userId)
    {
        return AffiliateCommission::where('affiliate_id', $userId)
            ->where('status', 'available')
            ->whereNull('withdrawal_request_id')
            ->where('available_at', '<=', now())
            ->sum('payable_amount');
    }

    public function getOnHoldBalance(int $userId)
    {
        return AffiliateCommission::where('affiliate_id', $userId)
            ->where('status', 'on_hold')
            ->where('available_at', '>', now())
            ->whereNull('withdrawal_request_id')
            ->sum('payable_amount');
    }

    public function getTotalEarnings(int $userId)
    {
        return AffiliateCommission::where('affiliate_id', $userId)
            ->whereIn('status', ['on_hold', 'available', 'paid', 'requested', 'processing'])
            ->sum('amount');
    }

    public function getTotalWithdrawn(int $userId)
    {
        return WithdrawalRequest::where('user_id', $userId)
            ->where('status', 'approved')
            ->sum('amount');
    }

    public function getEarnedCommissions(int $userId, int $perPage = 15)
    {
        return AffiliateCommission::with('reference') // ->latest()
            ->where('affiliate_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function getWithdrawalRequests(int $userId, int $perPage = 15)
    {
        return WithdrawalRequest::with('commissions')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function getAllWithdrawalRequests(int $perPage = 15)
    {
        return WithdrawalRequest::with(['user', 'commissions'])
            ->latest()
            ->paginate($perPage);
    }

    public function getWithdrawalRequestById($id)
    {
        return WithdrawalRequest::with(['user', 'commissions'])->findOrFail($id);
    }

    public function createWithdrawalRequest(array $data)
    {
        return WithdrawalRequest::create($data);
    }

    public function updateWithdrawalRequest($id, array $data)
    {
        $request = $this->getWithdrawalRequestById($id);
        $request->update($data);
        return $request;
    }

    public function updateCommissions(array $commissionIds, array $data)
    {
        return AffiliateCommission::whereIn('id', $commissionIds)->update($data);
    }

    public function getAvailableCommissionsByIds(array $commissionIds, int $userId)
    {
        return AffiliateCommission::where('affiliate_id', $userId)
            ->whereIn('id', $commissionIds)
            ->where('status', 'available')
            ->whereNull('withdrawal_request_id')
            ->where('available_at', '<=', now())
            ->lockForUpdate()
            ->get();
    }
}
