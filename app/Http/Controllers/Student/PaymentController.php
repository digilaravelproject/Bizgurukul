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
        try {
            $request->validate([
                'type' => 'required|in:coupon_package',
                'id' => 'required|integer'
            ]);

            $user = Auth::user();

            // DB Transaction not strictly needed here since we are just pulling
            // the entity and calling the service which handles its own DB writes (for order creation).
            // However, wrapping the entire initiation logic ensures atomicity if Service doesn't.
            \Illuminate\Support\Facades\DB::beginTransaction();

            if ($request->type === 'coupon_package') {
                $package = \App\Models\CouponPackage::findOrFail($request->id);
                $amount = $package->selling_price;
                $entity = $package;
            }

            $orderData = $this->paymentService->initiatePayment($user, $entity, $amount);

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['status' => 'success', 'data' => $orderData]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 422);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error("PaymentController Error [initiate] for user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to initiate payment. Please try again later.'], 500);
        }
    }

    /**
     * Verify Payment
     */
    public function verify(Request $request)
    {
        try {
            $request->validate([
                'razorpay_order_id' => 'required',
                'razorpay_payment_id' => 'required',
                'razorpay_signature' => 'required'
            ]);

            \Illuminate\Support\Facades\DB::beginTransaction();

            $payment = $this->paymentService->verifyPayment($request->all());

            // Post-payment actions based on paymentable type
            if ($payment->paymentable_type === \App\Models\CouponPackage::class) {
                // Issue Coupon
                $this->couponService->issueCouponForPayment($payment);
            }

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Payment verified successfully']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 422);
        } catch (Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            Log::error("PaymentController Error [verify] for Razorpay order {$request->razorpay_order_id}: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Payment verification failed. Please contact support.'], 500);
        }
    }
}
