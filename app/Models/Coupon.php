<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'coupon_type',
        'type',
        'value',
        'selected_courses',
        'selected_bundles',
        'expiry_date',
        'usage_limit',
        'used_count',
        'is_active'
    ];

    /**
     * JSON data ko array me cast karna
     */
    protected $casts = [
        'selected_courses' => 'array',
        'selected_bundles' => 'array',
        'expiry_date'      => 'date',
        'is_active'        => 'boolean',
    ];

    /**
     * Check if coupon is expired
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if usage limit is reached
     */
    public function isLimitReached()
    {
        return $this->used_count >= $this->usage_limit;
    }
}
