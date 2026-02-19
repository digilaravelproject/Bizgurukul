<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Razorpay\Api\Api;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $paymentRepo;
    protected $api;

    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepo = $paymentRepo;
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    /**
     * Initiate Payment (Create Razorpay Order)
     */
    public function initiatePayment(User $user, $payableEntity, float $amount, string $currency = 'INR')
    {
        try {
            $orderData = [
                'receipt'         => 'rcpt_' . $user->id . '_' . time(),
                'amount'          => round($amount * 100), // Paise
                'currency'        => $currency,
                'payment_capture' => 1
            ];

            $razorpayOrder = $this->api->order->create($orderData);

            // We don't create the Payment record here yet, or we could create it with status 'pending'.
            // Creating it pending helps track abandoned checkouts.

            $payment = $this->paymentRepo->create([
                'user_id'           => $user->id,
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount'            => $amount,
                'status'            => 'pending',
                'paymentable_type'  => get_class($payableEntity),
                'paymentable_id'    => $payableEntity->id,
            ]);

            return [
                'order_id' => $razorpayOrder['id'],
                'amount'   => $orderData['amount'],
                'key'      => config('services.razorpay.key'),
                'name'     => config('app.name'),
                'prefill'  => [
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'contact' => $user->mobile
                ]
            ];

        } catch (Exception $e) {
            Log::error("PaymentService Error [initiatePayment]: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify Payment
     */
    public function verifyPayment(array $data)
    {
        DB::beginTransaction();
        try {
            // 1. Verify Signature
            $attributes = [
                'razorpay_order_id'   => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature'  => $data['razorpay_signature']
            ];

            $this->api->utility->verifyPaymentSignature($attributes);

            // 2. Update Payment Record
            $payment = $this->paymentRepo->findByRazorpayOrderId($data['razorpay_order_id']);

            if (!$payment) {
                throw new Exception('Payment record not found for order ID.');
            }

            if ($payment->status === 'success') {
                DB::commit();
                return $payment; // Already verified
            }

            $this->paymentRepo->update($payment, [
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'status'              => 'success',
            ]);

            DB::commit();
            return $payment;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PaymentService Error [verifyPayment]: " . $e->getMessage());

            // Mark as failed if record exists
            if (isset($payment)) {
                $this->paymentRepo->update($payment, ['status' => 'failed']);
            }

            throw $e;
        }
    }
}
