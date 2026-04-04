<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Models\Setting;
use App\Repositories\PaymentRepository;
use App\Services\Gateways\PaymentGatewayFactory;
use App\Contracts\PaymentGatewayInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $paymentRepo;
    protected PaymentGatewayInterface $gateway;

    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepo = $paymentRepo;
        $this->gateway = PaymentGatewayFactory::make();
    }

    /**
     * Get the currently active gateway name.
     */
    public function getActiveGateway(): string
    {
        return $this->gateway->getGatewayName();
    }

    /**
     * Initiate Payment — routes to the active gateway (Razorpay or Cashfree).
     */
    public function initiatePayment(User $user, $payableEntity, float $amount, string $currency = 'INR')
    {
        try {
            $gatewayName = $this->gateway->getGatewayName();

            $orderResult = $this->gateway->createOrder([
                'amount'   => $amount,
                'receipt'  => 'rcpt_' . $user->id . '_' . time(),
                'currency' => $currency,
                'notes'    => [
                    'user_id'   => $user->id,
                    'entity_id' => $payableEntity->id,
                ],
                'customer' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->mobile,
                ],
                'return_url' => url('/'),
            ]);

            // Create payment record with gateway tracking
            $payment = $this->paymentRepo->create([
                'user_id'            => $user->id,
                'razorpay_order_id'  => $gatewayName === 'razorpay' ? $orderResult['order_id'] : null,
                'amount'             => $amount,
                'status'             => 'pending',
                'paymentable_type'   => get_class($payableEntity),
                'paymentable_id'     => $payableEntity->id,
                'payment_gateway'    => $gatewayName,
                'gateway_order_id'   => $orderResult['order_id'],
                'gateway_payment_id' => null,
            ]);

            // Build a unified response for both gateways
            $response = [
                'gateway'    => $gatewayName,
                'order_id'   => $orderResult['order_id'],
                'amount'     => $orderResult['amount'],
                'key'        => $orderResult['key'],
                'name'       => config('app.name'),
                'prefill'    => [
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'contact' => $user->mobile,
                ],
            ];

            // Cashfree-specific fields
            if ($gatewayName === 'cashfree') {
                $response['session_id']  = $orderResult['session_id'] ?? null;
                $response['environment'] = $orderResult['environment'] ?? 'sandbox';
            }

            return $response;

        } catch (Exception $e) {
            Log::error("PaymentService Error [initiatePayment]: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify Payment — routes to the active gateway.
     */
    public function verifyPayment(array $data)
    {
        DB::beginTransaction();
        try {
            $payment = null;

            // Determine which gateway made this payment
            if (!empty($data['razorpay_order_id'])) {
                // Razorpay flow
                $gateway = PaymentGatewayFactory::make('razorpay');

                $verifyResult = $gateway->verifyPayment([
                    'razorpay_order_id'   => $data['razorpay_order_id'],
                    'razorpay_payment_id' => $data['razorpay_payment_id'],
                    'razorpay_signature'  => $data['razorpay_signature'],
                ]);

                // Find payment by razorpay order ID (backward compatible)
                $payment = $this->paymentRepo->findByRazorpayOrderId($data['razorpay_order_id']);

                if (!$payment) {
                    // Also try the generic gateway_order_id
                    $payment = Payment::where('gateway_order_id', $data['razorpay_order_id'])->first();
                }

                if (!$payment) {
                    throw new Exception('Payment record not found for order ID.');
                }

                if ($payment->status === 'success') {
                    DB::commit();
                    return $payment;
                }

                if (!$verifyResult['verified']) {
                    throw new Exception('Payment signature verification failed.');
                }

                $this->paymentRepo->update($payment, [
                    'razorpay_payment_id' => $data['razorpay_payment_id'],
                    'gateway_payment_id'  => $data['razorpay_payment_id'],
                    'status'              => 'success',
                ]);

            } elseif (!empty($data['cashfree_order_id'])) {
                // Cashfree flow
                $gateway = PaymentGatewayFactory::make('cashfree');

                $verifyResult = $gateway->verifyPayment([
                    'order_id' => $data['cashfree_order_id'],
                ]);

                $payment = Payment::where('gateway_order_id', $data['cashfree_order_id'])->first();

                if (!$payment) {
                    throw new Exception('Payment record not found for Cashfree order ID.');
                }

                if ($payment->status === 'success') {
                    DB::commit();
                    return $payment;
                }

                if (!$verifyResult['verified']) {
                    throw new Exception('Cashfree payment verification failed: ' . ($verifyResult['error'] ?? 'Unknown'));
                }

                $this->paymentRepo->update($payment, [
                    'gateway_payment_id' => $verifyResult['payment_id'],
                    'status'             => 'success',
                ]);

            } else {
                throw new Exception('No valid order ID provided for payment verification.');
            }

            DB::commit();
            return $payment;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("PaymentService Error [verifyPayment]: " . $e->getMessage());

            if (isset($payment)) {
                $this->paymentRepo->update($payment, ['status' => 'failed']);
            }

            throw $e;
        }
    }
}
