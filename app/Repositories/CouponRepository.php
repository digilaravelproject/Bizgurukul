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
        $query = Coupon::latest();

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
        return Coupon::find($id);
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
        return Coupon::destroy($id);
    }

    /**
     * Toggle Status
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
}
