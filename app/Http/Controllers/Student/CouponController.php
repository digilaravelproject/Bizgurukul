<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Repositories\CouponRepository;
use App\Repositories\CouponPackageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        try {
            $status = $request->query('status', 'active'); // Default to active
            $coupons = $this->couponRepo->getCouponsByUser(Auth::id(), $status);

            return view('student.coupons.index', compact('coupons', 'status'));
        } catch (Exception $e) {
            Log::error("Error loading coupons index for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Unable to load your coupons.');
        }
    }

    /**
     * Coupon Store (List available packages)
     */
    public function store()
    {
        try {
            $packages = $this->packageRepo->getActivePackages();
            return view('student.coupons.store', compact('packages'));
        } catch (Exception $e) {
            Log::error("Error loading coupon store for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Unable to load the coupon store.');
        }
    }

    /**
     * Initiate Purchase (Create Razorpay Order)
     */
    public function initiatePurchase(Request $request)
    {
        try {
            $request->validate([
                'package_id' => 'required|exists:coupon_packages,id'
            ]);

            DB::beginTransaction();

            $user = Auth::user();
            $package = $this->packageRepo->find($request->package_id);

            if (!$package || !$package->is_active) {
                return response()->json(['status' => 'error', 'message' => 'Package not available'], 400);
            }

            // Use PaymentService to initiate
            $paymentService = app(\App\Services\PaymentService::class);
            $orderData = $paymentService->initiatePayment($user, $package, $package->selling_price);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $orderData
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error initiating coupon purchase for user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to initialize payment. Please try again.'], 500);
        }
    }

    /**
     * Verify Purchase and Issue Coupon
     */
    public function verifyPurchase(Request $request)
    {
        try {
            $request->validate([
                'razorpay_order_id' => 'required',
                'razorpay_payment_id' => 'required',
                'razorpay_signature' => 'required'
            ]);

            DB::beginTransaction();

            $paymentService = app(\App\Services\PaymentService::class);
            $payment = $paymentService->verifyPayment($request->all());

            // Issue Coupon if payment is for a package
            if ($payment->paymentable_type === \App\Models\CouponPackage::class) {
                $coupon = $this->couponService->issueCouponForPayment($payment);

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Coupon purchased successfully!',
                    'code' => $coupon->code
                ]);
            }

            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Invalid payment type associated with this order.'], 400);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error verifying coupon purchase for order {$request->razorpay_order_id}: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Payment verification failed. Please contact support.'], 500);
        }
    }

    /**
     * Transfer Coupon
     */
    public function transfer(Request $request)
    {
        try {
            $request->validate([
                'coupon_id' => 'required|exists:coupons,id',
                'recipient_email' => 'required|email|exists:users,email'
            ]);

            DB::beginTransaction();

            $recipient = \App\Models\User::where('email', $request->recipient_email)->firstOrFail();

            if ($recipient->id === Auth::id()) {
                return response()->json(['status' => 'error', 'message' => 'You cannot transfer a coupon to yourself.'], 422);
            }

            $this->couponService->transferCoupon(Auth::user(), $request->coupon_id, $recipient->id);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Coupon transferred successfully!']);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Recipient user not found.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 422);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error transferring coupon {$request->coupon_id} from user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to transfer coupon. Please try again or check coupon validity.'], 500);
        }
    }
}
