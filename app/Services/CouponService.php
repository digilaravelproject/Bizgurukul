<?php

namespace App\Services;

use App\Repositories\CouponRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CouponService
{
    protected $repository;

    public function __construct(CouponRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle Create or Update Logic with Transaction
     */
    public function handleSave(array $data, ?int $id = null)
    {
        DB::beginTransaction();

        try {
            // 1. Data Formatting
            $formattedData = [
                'code'           => strtoupper(trim($data['code'])),
                'coupon_type'    => $data['coupon_type'], // general or specific
                'type'           => $data['type'],        // fixed or percentage
                'value'          => $data['value'],
                'expiry_date'    => $data['expiry_date'],
                'usage_limit'    => $data['usage_limit'] ?? 1,
                'is_active'      => isset($data['is_active']) ? (bool)$data['is_active'] : true,
            ];

            // 2. Logic for Specific Coupons
            // Agar General hai to arrays null hone chahiye, agar Specific hai to data aana chahiye
            if ($data['coupon_type'] === 'specific') {
                $formattedData['selected_courses'] = $data['courses'] ?? [];
                $formattedData['selected_bundles'] = $data['bundles'] ?? [];
            } else {
                $formattedData['selected_courses'] = null;
                $formattedData['selected_bundles'] = null;
            }

            // 3. Save to Repo
            $coupon = $this->repository->updateOrCreate(
                ['id' => $id],
                $formattedData
            );

            DB::commit();
            return $coupon;

        } catch (Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            Log::error("Coupon Save Error: " . $e->getMessage());
            throw $e; // Re-throw to controller
        }
    }

    /**
     * Get Coupon for Edit
     * Note: No need for json_decode because Model $casts handles it.
     */
    public function getCouponForEdit(int $id)
    {
        $coupon = $this->repository->findById($id);

        if (!$coupon) {
            throw new Exception("Coupon not found.");
        }

        return $coupon;
    }

    public function toggleStatus(int $id)
    {
        try {
            return $this->repository->toggleStatus($id);
        } catch (Exception $e) {
            Log::error("Coupon Toggle Error: " . $e->getMessage());
            throw new Exception("Unable to change status.");
        }
    }

    public function deleteCoupon(int $id)
    {
        DB::beginTransaction();
        try {
            $deleted = $this->repository->delete($id);
            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Coupon Delete Error: " . $e->getMessage());
            throw new Exception("Unable to delete coupon.");
        }
    }

    public function getPaginatedCoupons($request)
    {
        $search = $request->search ? trim($request->search) : null;
        return $this->repository->findAll(10, $search);
    }
}
