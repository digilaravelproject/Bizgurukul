<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponPackage;
use App\Models\User;
use App\Repositories\CouponRepository;
use App\Repositories\CouponPackageRepository;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class CouponService
{
    protected $couponRepo;
    protected $packageRepo;

    public function __construct(CouponRepository $couponRepo, CouponPackageRepository $packageRepo)
    {
        $this->couponRepo = $couponRepo;
        $this->packageRepo = $packageRepo;
    }

    /**
     * Create Coupon Package
     */
    public function createPackage(array $data)
    {
        // Validation can be handled in Controller or FormRequest
        return $this->packageRepo->create($data);
    }

    /**
     * Update Coupon Package
     */
    public function updatePackage(int $id, array $data)
    {
        $package = $this->packageRepo->find($id);
        if (!$package) {
            throw new Exception('Package not found');
        }
        return $this->packageRepo->update($package, $data);
    }

    /**
     * Purchase Package
     * Handles payment (mocked here), usage tracking, and coupon generation
     */
    public function purchasePackage(User $user, int $packageId)
    {
        return DB::transaction(function () use ($user, $packageId) {
            $package = $this->packageRepo->find($packageId);

            if (!$package || !$package->is_active) {
                throw new Exception('Package is not available.');
            }

            // TODO: Integrate Wallet/Payment logic here.
            // For now, assume payment is successful.

            // Generate Coupon
            $code = $this->generateUniqueCode();

            $couponData = [
                'user_id' => $user->id,
                'package_id' => $package->id,
                'code' => $code,
                'type' => $package->type,
                'value' => $package->discount_value,
                'expiry_date' => Carbon::today()->addMonths(3), // 3 Months Validity
                'usage_limit' => 1,
                'used_count' => 0,
                'is_active' => true,
                'status' => 'active',
                'purchased_at' => now(),
                'coupon_type' => 'general', // Or specific based on package
                'selected_courses' => $package->selected_courses,
                'selected_bundles' => $package->selected_bundles,
                'couponable_type' => $package->couponable_type,
                'couponable_id' => $package->couponable_id,
            ];

            $coupon = $this->couponRepo->updateOrCreate(['code' => $code], $couponData);

            // Update Package Usage Link (Optional tracking)
            $package->increment('used_count'); // Tracks how many times package was sold

            return $coupon;
        });
    }

    /**
     * Transfer Coupon
     */
    public function transferCoupon(User $sender, int $couponId, int $recipientId)
    {
        return DB::transaction(function () use ($sender, $couponId, $recipientId) {
            $coupon = $this->couponRepo->findByIdLocked($couponId);

            if (!$coupon) {
                throw new Exception('Coupon not found.');
            }

            if ($coupon->user_id !== $sender->id) {
                throw new Exception('You do not own this coupon.');
            }

            if ($coupon->status !== 'active' || $coupon->isExpired() || $coupon->isLimitReached()) {
                throw new Exception('Coupon is not valid for transfer.');
            }

            if ($sender->id === $recipientId) {
                throw new Exception('Cannot transfer coupon to yourself.');
            }

            $recipient = User::find($recipientId);
            if (!$recipient) {
                throw new Exception('Recipient not found.');
            }

            // Perform Transfer
            $coupon->user_id = $recipientId;
            $coupon->transferred_from = $sender->id;
            $coupon->save();

            // Log Transfer
            $this->couponRepo->logTransfer($coupon->id, $sender->id, $recipientId);

            return $coupon;
        });
    }

    /**
     * Generate Unique Code
     * Format: ALPHANUMERIC (8-14 chars)
     */
    private function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(12));
        } while ($this->couponRepo->findByCode($code));

        return $code;
    }

    /**
     * Get All Coupons (Admin)
     */
    public function getPaginatedCoupons($request)
    {
        return $this->couponRepo->findAll(10, $request->search);
    }

    /**
     * Create/Update Coupon (Admin Manual)
     */
    public function handleSave(array $data, ?int $id = null)
    {
        // Add defaults for manual creation
        if (!$id && empty($data['code'])) {
            $data['code'] = $this->generateUniqueCode();
        }

        // Manual creation by admin usually means no package, so package_id null
        $data['status'] = $data['status'] ?? 'active';
        $data['is_active'] = isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : true;

        if (isset($data['expiry_date'])) {
             $data['expiry_date'] = Carbon::parse($data['expiry_date']);
        }

        return $this->couponRepo->updateOrCreate(['id' => $id], $data);
    }

    /**
     * Get Coupon for Edit
     */
    public function getCouponForEdit($id)
    {
        return $this->couponRepo->findById($id);
    }

    /**
     * Delete Coupon
     */
    public function deleteCoupon($id)
    {
        return $this->couponRepo->delete($id);
    }

    /**
     * Toggle Status
     */
    public function toggleStatus($id)
    {
        return $this->couponRepo->toggleStatus($id);
    }
}
