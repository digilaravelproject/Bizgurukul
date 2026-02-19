<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Repositories\CouponRepository;
use App\Repositories\CouponPackageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class CouponController extends Controller
{
    protected $couponService;
    protected $couponRepo;
    protected $packageRepo;

    public function __construct(
        CouponService $couponService,
        CouponRepository $couponRepo,
        CouponPackageRepository $packageRepo
    ) {
        $this->couponService = $couponService;
        $this->couponRepo = $couponRepo;
        $this->packageRepo = $packageRepo;
    }

    /**
     * My Coupons (List active, used, expired)
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'active'); // Default to active
        $coupons = $this->couponRepo->getCouponsByUser(Auth::id(), $status);

        return view('student.coupons.index', compact('coupons', 'status'));
    }

    /**
     * Coupon Store (List available packages)
     */
    public function store()
    {
        $packages = $this->packageRepo->getActivePackages();
        return view('student.coupons.store', compact('packages'));
    }

    /**
     * Purchase Verification / Process
     */
    /**
     * Initiate Purchase (Create Razorpay Order)
     */
    public function initiatePurchase(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:coupon_packages,id'
        ]);

        try {
            $user = Auth::user();
            $package = $this->packageRepo->find($request->package_id);

            if (!$package || !$package->is_active) {
                return response()->json(['status' => 'error', 'message' => 'Package not available'], 400);
            }

            // Use PaymentService to initiate
            $paymentService = app(\App\Services\PaymentService::class);
            $orderData = $paymentService->initiatePayment($user, $package, $package->selling_price);

            return response()->json([
                'status' => 'success',
                'data' => $orderData
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Verify Purchase and Issue Coupon
     */
    public function verifyPurchase(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required'
        ]);

        try {
            $paymentService = app(\App\Services\PaymentService::class);
            $payment = $paymentService->verifyPayment($request->all());

            // Issue Coupon if payment is for a package
            if ($payment->paymentable_type === \App\Models\CouponPackage::class) {
                $coupon = $this->couponService->issueCouponForPayment($payment);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Coupon purchased successfully!',
                    'code' => $coupon->code
                ]);
            }

            return response()->json(['status' => 'error', 'message' => 'Invalid payment type'], 400);

        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Transfer Coupon
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,id',
            'recipient_email' => 'required|email|exists:users,email'
        ]);

        try {
            $recipient = \App\Models\User::where('email', $request->recipient_email)->firstOrFail();

            $this->couponService->transferCoupon(Auth::user(), $request->coupon_id, $recipient->id);

            return response()->json(['status' => 'success', 'message' => 'Coupon transferred successfully!']);
        } catch (Exception $e) {
             return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
