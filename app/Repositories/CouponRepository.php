<?php

namespace App\Repositories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CouponRepository
{
    /**
     * Get paginated coupons with optional search
     */
    public function findAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        $query = Coupon::latest()->with(['owner', 'package']);

        if ($search) {
            $query->where('code', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    /**
     * Find a coupon by ID
     */
    public function findById(int $id): ?Coupon
    {
        return Coupon::with(['owner', 'package'])->find($id);
    }

    /**
     * Find a coupon and lock it for update (Pessimistic Locking)
     * Prevents race conditions during heavy write operations
     */
    public function findByIdLocked(int $id): ?Coupon
    {
        return Coupon::where('id', $id)->lockForUpdate()->first();
    }

    /**
     * Create or Update
     * Service layer se jo formatted array aayega, wo seedha yahan save hoga.
     */
    public function updateOrCreate(array $attributes, array $values): Coupon
    {
        return Coupon::updateOrCreate($attributes, $values);
    }

    /**
     * Delete
     */
    public function delete(int $id): bool
    {
        return Coupon::destroy($id) > 0;
    }

    /**
     * Toggle Status
     * Returns true if status changed, false otherwise
     */
    public function toggleStatus(int $id): bool
    {
        $coupon = $this->findById($id);

        if ($coupon) {
            $coupon->is_active = !$coupon->is_active;
            return $coupon->save();
        }

        return false;
    }

    /**
     * Find Valid Coupon by Code (For User/Frontend Side)
     */
    public function findByCode(string $code): ?Coupon
    {
        return Coupon::where('code', $code)->active()->first();
    }
    /**
     * Get Coupons by User
     * With optional status filter (active, used, expired)
     */
    public function getCouponsByUser(int $userId, ?string $status = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Coupon::where('user_id', $userId)->with('package');

        if ($status) {
            if ($status === 'active') {
                $query->active();
            } elseif ($status === 'expired') {
                $query->where(function ($q) {
                    $q->whereNotNull('expiry_date')->where('expiry_date', '<', now());
                });
            } elseif ($status === 'used') {
                $query->whereColumn('used_count', '>=', 'usage_limit');
            }
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Log Transfer details
     */
    public function logTransfer(int $couponId, int $fromUserId, int $toUserId): void
    {
        \App\Models\CouponTransfer::create([
            'coupon_id' => $couponId,
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            'transferred_at' => now(),
        ]);
    }

    /**
     * Check if user reached limit for a specific package
     * (Assuming limit: 1 active coupon per package per user)
     */
    public function hasActiveCouponForPackage(int $userId, int $packageId): bool
    {
        return Coupon::where('user_id', $userId)
            ->where('package_id', $packageId)
            ->active()
            ->exists();
    }
}
