<?php

namespace App\Services;

use App\Repositories\CouponRepository;
use Illuminate\Support\Facades\DB;
use Exception;

class CouponService
{
    protected $repository;

    public function __construct(CouponRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleSave(array $data, ?int $id = null)
    {
        return DB::transaction(function () use ($data, $id) {

            // Formatting Logic
            $formattedData = [
                'code'             => strtoupper($data['code']),
                'coupon_type'      => $data['coupon_type'],
                'type'             => $data['type'],
                'value'            => $data['value'],
                'expiry_date'      => $data['expiry_date'],
                'usage_limit'      => $data['usage_limit'] ?? 1,
            ];

            // Scope Logic: Specific hai toh array save karo, nahi toh null
            if ($data['coupon_type'] === 'specific') {
                // Ensure arrays are stored properly (JSON encoded automatically by cast or manually if needed)
                $formattedData['selected_courses'] = $data['courses'] ?? [];
                $formattedData['selected_bundles'] = $data['bundles'] ?? [];
            } else {
                $formattedData['selected_courses'] = null;
                $formattedData['selected_bundles'] = null;
            }

            return $this->repository->updateOrCreate(
                ['id' => $id],
                $formattedData
            );
        });
    }

    public function toggleStatus(int $id)
    {
        return $this->repository->toggleStatus($id);
    }

    public function deleteCoupon(int $id)
    {
        return DB::transaction(function () use ($id) {
            return $this->repository->delete($id);
        });
    }

    public function getPaginatedCoupons($request)
    {
        // Search trim fix
        $search = $request->search ? trim($request->search) : null;
        return $this->repository->findAll(10, $search);
    }

    public function getCouponForEdit(int $id)
    {
        $coupon = $this->repository->findById($id);

        // Data Formatting for Frontend consistency
        if ($coupon) {
            // Agar database mein JSON string hai toh decode karo, agar array cast hai toh direct use karo
            // Safety Check: Hamesha array return karo
            $coupon->selected_courses = is_string($coupon->selected_courses)
                ? json_decode($coupon->selected_courses, true) ?? []
                : $coupon->selected_courses ?? [];

            $coupon->selected_bundles = is_string($coupon->selected_bundles)
                ? json_decode($coupon->selected_bundles, true) ?? []
                : $coupon->selected_bundles ?? [];
        }

        return $coupon;
    }
}
