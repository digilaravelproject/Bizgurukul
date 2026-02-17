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

    public function __construct(\App\Services\CommissionCalculatorService $commissionService)
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
            'amount'          => $course->final_price * 100, // Use final_price
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        $razorpayOrder = $this->api->order->create($orderData);

        // Store pending payment in database
        Payment::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'razorpay_order_id' => $razorpayOrder['id'],
            'amount' => $course->final_price,
            'status' => 'pending',
        ]);

        return response()->json([
            'order_id' => $razorpayOrder['id'],
            'amount' => $orderData['amount'],
            'key' => env('RAZORPAY_KEY'),
            'course_name' => $course->title,
        ]);
    }

    // Add method for Bundle Order if needed, or genericize later.
    // For now, focusing on fixing verifyPayment logic as per request.

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

            // DB Transaction for ACID compliance
            \Illuminate\Support\Facades\DB::beginTransaction();

            try {
                // Update Database
                $payment = Payment::with(['course', 'bundle'])->where('razorpay_order_id', $orderId)->lockForUpdate()->firstOrFail();

                if ($payment->status !== 'success') {
                    $payment->update([
                        'status' => 'success',
                        'razorpay_payment_id' => $paymentId
                    ]);

                    // Determine Product
                    $product = $payment->bundle ?? $payment->course;

                    // Commission Logic
                    $user = Auth::user();
                    // Check if referred (either by direct referrer or via cookie/affiliate link logic)
                    // Assuming user->referred_by serves as the sponsor
                    if ($user && $user->referrer) {
                        $sponsor = $user->referrer;

                        // Calculate Commission
                        // Only if product is Bundle (as per Capped Logic spec?)
                        // Or if service handles Course too (we put logic for Bundle type hinting).
                        // If product is Course, we might need a fallback or service update.
                        // Assuming for now it works for Bundles mostly. If Course, logic might be simple.

                        $commissionAmount = 0;
                        if ($product instanceof \App\Models\Bundle) {
                             $commissionAmount = $this->commissionService->calculateCommission($sponsor, $product);
                        } elseif ($product instanceof Course) {
                             // Fallback for course if needed, or simple percentage?
                             // User spec only detailed Bundle logic.
                             // Using course->commission_value directly?
                             $commissionAmount = $product->commission_value ?? 0;
                        }

                        if ($commissionAmount > 0) {
                            \App\Models\AffiliateCommission::create([
                                'affiliate_id' => $sponsor->id,
                                'referred_user_id' => $user->id,
                                'amount' => $commissionAmount,
                                'status' => 'pending',
                                'reference_id' => $product->id,
                                'reference_type' => get_class($product),
                                'notes' => 'Commission for ' . class_basename($product) . ': ' . $product->title,
                            ]);

                            // Credit Wallet (Optional, depends if commission is auto-approved or pending)
                            // If pending, no wallet credit yet. Admin approves.
                        }
                    }
                }

                \Illuminate\Support\Facades\DB::commit();
                return response()->json(['status' => 'success', 'message' => 'Payment Verified']);

            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
