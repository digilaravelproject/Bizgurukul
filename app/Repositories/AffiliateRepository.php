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
}
