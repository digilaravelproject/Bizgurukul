<?php

namespace App\Repositories;

use App\Models\CouponPackage;
use Illuminate\Pagination\LengthAwarePaginator;

class CouponPackageRepository
{
    /**
     * Get all active coupon packages
     */
    public function getActivePackages(int $perPage = 10): LengthAwarePaginator
    {
        return CouponPackage::where('is_active', true)->latest()->paginate($perPage);
    }

    /**
     * Get all packages for Admin with optional search and filter
     */
    public function getAllPackages(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = CouponPackage::latest();

        if ($search) {
             $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    /**
     * Create Coupon Package
     */
    public function create(array $data): CouponPackage
    {
        return CouponPackage::create($data);
    }

    /**
     * Update Coupon Package
     */
    public function update(CouponPackage $package, array $data): CouponPackage
    {
        $package->update($data);
        return $package;
    }

    /**
     * Find by ID
     */
    public function find(int $id): ?CouponPackage
    {
        return CouponPackage::find($id);
    }

    /**
     * Delete Package
     */
    public function delete(int $id): bool
    {
        return CouponPackage::destroy($id) > 0;
    }

    /**
     * Toggle Status
     */
    public function toggleStatus(int $id): bool
    {
        $package = $this->find($id);
        if ($package) {
            $package->is_active = !$package->is_active;
            return $package->save();
        }
        return false;
    }
}
