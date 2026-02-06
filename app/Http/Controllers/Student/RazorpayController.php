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

    public function __construct()
    {
        $this->api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
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
            $payment = Payment::where('razorpay_order_id', $orderId)->first();
            $payment->update([
                'status' => 'success',
                'razorpay_payment_id' => $paymentId
            ]);

            return response()->json(['status' => 'success', 'message' => 'Payment Verified']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }
}
