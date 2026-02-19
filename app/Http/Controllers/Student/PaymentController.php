<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $couponService;

    public function __construct(PaymentService $paymentService, CouponService $couponService)
    {
        $this->paymentService = $paymentService;
        $this->couponService = $couponService;
    }

    /**
     * Initiate Payment for generic items (e.g. Coupon Packages)
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:coupon_package',
            'id' => 'required|integer'
        ]);

        try {
            $user = Auth::user();

            if ($request->type === 'coupon_package') {
                // We need to fetch the package to get amount
                // Since we don't have PackageService injected, we might need a way to get entity.
                // For now, let's assume CouponService has a way or we inject Repository.
                // Better: PaymentController shouldn't know about Coupon Packages details specifically?
                // Actually, it's a bridge. Let's start with CouponController calling PaymentService.
                // But user requested PaymentController.

                // Let's delegate to specific services based on type, or fetch entity here.
                // Since this is generic, we need the entity.

                if ($request->type === 'coupon_package') {
                    $package = \App\Models\CouponPackage::findOrFail($request->id); // Direct model for simplicity or inject Repo
                    $amount = $package->selling_price;
                    $entity = $package;
                }

                $orderData = $this->paymentService->initiatePayment($user, $entity, $amount);

                return response()->json(['status' => 'success', 'data' => $orderData]);
            }

        } catch (Exception $e) {
            Log::error("PaymentController Error [initiate]: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Verify Payment
     */
    public function verify(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required'
        ]);

        try {
            $payment = $this->paymentService->verifyPayment($request->all());

            // Post-payment actions based on paymentable type
            if ($payment->paymentable_type === \App\Models\CouponPackage::class) {
                // Issue Coupon
                $this->couponService->issueCouponForPayment($payment);
            }

            return response()->json(['status' => 'success', 'message' => 'Payment verified successfully']);

        } catch (Exception $e) {
            Log::error("PaymentController Error [verify]: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Payment verification failed'], 400);
        }
    }
}
