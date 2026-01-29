<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;

class CouponService
{
    /**
     * Store or Update Coupon with Multiple Selection & Service Logic
     */
    public function saveCoupon(array $data, $id = null)
    {
        return DB::transaction(function () use ($data, $id) {
            return Coupon::updateOrCreate(
                ['id' => $id],
                [
                    'code'             => strtoupper($data['code']),
                    'coupon_type'      => $data['coupon_type'],
                    'type'             => $data['type'], // 'fixed' or 'percentage'
                    'value'            => $data['value'],
                    'expiry_date'      => $data['expiry_date'],
                    'usage_limit'      => $data['usage_limit'] ?? 1,

                    // Direct array pass karein
                    'selected_courses' => ($data['coupon_type'] === 'specific') ? ($data['courses'] ?? []) : null,
                    'selected_bundles' => ($data['coupon_type'] === 'specific') ? ($data['bundles'] ?? []) : null,
                ]
            );
        });
    }
}
