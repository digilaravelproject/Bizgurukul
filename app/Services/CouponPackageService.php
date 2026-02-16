<?php

namespace App\Services;

use App\Models\CouponPackage;

class CouponPackageService
{
    public function handleSave(array $data, ?int $id = null)
    {
        return CouponPackage::updateOrCreate(
            ['id' => $id],
            [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'] ?? 'fixed',
                'selling_price' => $data['selling_price'],
                'discount_value' => $data['discount_value'],
                'selected_courses' => $data['courses'] ?? [],
                'selected_bundles' => $data['bundles'] ?? [],
                'is_active' => filter_var($data['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'couponable_type' => null,
                'couponable_id' => null,
            ]
        );
    }
}
