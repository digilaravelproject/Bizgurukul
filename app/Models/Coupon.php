<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'transferred_from',
        'purchased_at',
        'status',
        'code',
        'type',
        'value',
        'expiry_date',
        'usage_limit',
        'used_count',
        'is_active',
        'coupon_type',
        'selected_courses',
        'selected_bundles',
        'couponable_type',
        'couponable_id'
    ];

    protected $casts = [
        'selected_courses' => 'array',
        'selected_bundles' => 'array',
        'expiry_date'      => 'date',
        'is_active'        => 'boolean',
        'value'            => 'float',
        'usage_limit'      => 'integer',
        'used_count'       => 'integer',
        'purchased_at'     => 'datetime',
    ];

    /**
     * Scope: Active and Valid Coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function ($q) {
                         $q->whereNull('expiry_date')
                           ->orWhere('expiry_date', '>=', Carbon::today());
                     })
                     ->whereColumn('used_count', '<', 'usage_limit');
    }

    /**
     * Check if coupon is expired by date
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check usage limit
     */
    public function isLimitReached(): bool
    {
        return $this->used_count >= $this->usage_limit;
    }

    /**
     * Validate if coupon applies to specific Cart Items
     * * @param array $cartCourseIds Array of course IDs in cart
     * @param array $cartBundleIds Array of bundle IDs in cart
     * @return bool
     */
    public function isValidForItems(array $cartCourseIds = [], array $cartBundleIds = []): bool
    {
        if ($this->coupon_type === 'general') {
            return true;
        }

        // Check Courses
        if (!empty($this->selected_courses)) {
            $commonCourses = array_intersect($this->selected_courses, $cartCourseIds);
            if (count($commonCourses) > 0) return true;
        }

        // Check Bundles
        if (!empty($this->selected_bundles)) {
            $commonBundles = array_intersect($this->selected_bundles, $cartBundleIds);
            if (count($commonBundles) > 0) return true;
        }

        return false;
    }

    /**
     * Calculate Discount Amount
     */
    public function calculateDiscount($totalAmount): float
    {
        if ($this->type === 'percentage') {
            return ($totalAmount * $this->value) / 100;
        }

        return min($this->value, $totalAmount);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package()
    {
        return $this->belongsTo(CouponPackage::class, 'package_id');
    }

    public function transfers()
    {
        return $this->hasMany(CouponTransfer::class);
    }
}
