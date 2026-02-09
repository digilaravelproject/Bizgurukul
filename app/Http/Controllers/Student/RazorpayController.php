<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class RazorpayController extends Controller
{
    private $api;
    private $commissionService;

    public function __construct(\App\Services\CommissionRuleService $commissionService)
    {
        $this->api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $this->commissionService = $commissionService;
    }

    public function createOrder(Request $request, $courseId)
    {
        $course = Course::findOrFail($courseId);

        // Razorpay expects amount in paise (1 INR = 100 Paise)
        $orderData = [
            'receipt'         => 'rcpt_' . time(),
            'amount'          => $course->price * 100,
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $razorpayOrder = $this->api->order->create($orderData);

        // Store pending payment in database
        Payment::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'razorpay_order_id' => $razorpayOrder['id'],
            'amount' => $course->price,
            'status' => 'pending',
        ]);

        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'amount' => $orderData['amount'],
            'key' => env('RAZORPAY_KEY'),
            'course_name' => $course->title,
        ]);
    }

    public function verifyPayment(Request $request)
    {
        $signature = $request->razorpay_signature;
        $paymentId = $request->razorpay_payment_id;
        $orderId = $request->razorpay_order_id;

        try {
            $attributes = [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature
            ];

            $this->api->utility->verifyPaymentSignature($attributes);

            // Update Database
            $payment = Payment::with('course')->where('razorpay_order_id', $orderId)->firstOrFail();

            if ($payment->status !== 'success') {
                $payment->update([
                    'status' => 'success',
                    'razorpay_payment_id' => $paymentId
                ]);

                // Commission Logic
                $user = Auth::user();
                if ($user && $user->referred_by) {
                    $commissionAmount = $this->commissionService->calculateCommission($user->referred_by, $payment->course);

                    if ($commissionAmount > 0) {
                        \App\Models\AffiliateCommission::create([
                            'affiliate_id' => $user->referred_by,
                            'referred_user_id' => $user->id,
                            'amount' => $commissionAmount,
                            'status' => 'pending', // Or 'approved' based on policy
                            'commission_type' => 'sale',
                            'product_id' => $payment->course_id,
                            'product_type' => get_class($payment->course),
                            'notes' => 'Commission for Course Sale: ' . $payment->course->title,
                        ]);
                    }
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Payment Verified']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
