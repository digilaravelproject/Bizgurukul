<?php

namespace App\Repositories;

use App\Models\AffiliateCommission;
use App\Models\WalletTransaction;

class AffiliateRepository
{
    protected $commissionModel;
    protected $walletModel;

    public function __construct(AffiliateCommission $commissionModel, WalletTransaction $walletModel)
    {
        $this->commissionModel = $commissionModel;
        $this->walletModel = $walletModel;
    }

    public function getPaginatedCommissions($perPage = 20)
    {
        return $this->commissionModel->with(['affiliate', 'referredUser', 'reference'])
            ->latest()
            ->paginate($perPage);
    }

    public function findCommission($id)
    {
        return $this->commissionModel->with('affiliate')->find($id);
    }

    public function saveCommission(AffiliateCommission $commission)
    {
        return $commission->save();
    }

    public function createWalletTransaction(array $data)
    {
        return $this->walletModel->create($data);
    }

    // --- Link Management ---

    public function createLink(array $data)
    {
        return \App\Models\AffiliateLink::create($data);
    }

    public function findLinkBySlug($slug)
    {
        return \App\Models\AffiliateLink::where('slug', $slug)
            ->where('is_deleted', false)
            ->first();
    }

    public function softDeleteLink($id)
    {
        /** @var \App\Models\AffiliateLink|null $link */
        $link = \App\Models\AffiliateLink::find($id);

        if ($link) {
            /** @var \App\Models\AffiliateLink $link */
            $link->is_deleted = true;
            $link->save();
            return true;
        }
        return false;
    }

    public function getAffiliateLinks($userId, $perPage = 10)
    {
        return \App\Models\AffiliateLink::where('user_id', $userId)
            ->where('is_deleted', false)
            ->latest()
            ->paginate($perPage);
    }
}
