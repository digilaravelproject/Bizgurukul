<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Setting;
use Razorpay\Api\Api;
use Exception;

class RazorpayGateway implements PaymentGatewayInterface
{
    protected Api $api;
    protected string $key;

    public function __construct()
    {
        // Admin panel settings override .env defaults
        $this->key = Setting::get('razorpay_key') ?: config('services.razorpay.key');
        $secret = Setting::get('razorpay_secret') ?: config('services.razorpay.secret');

        $this->api = new Api($this->key, $secret);
    }

    public function createOrder(array $data): array
    {
        $orderData = [
            'receipt'         => $data['receipt'] ?? 'rcpt_' . time(),
            'amount'          => intval(round($data['amount'] * 100)), // Paise
            'currency'        => $data['currency'] ?? 'INR',
            'payment_capture' => 1,
        ];

        if (!empty($data['notes'])) {
            $orderData['notes'] = $data['notes'];
        }

        $razorpayOrder = $this->api->order->create($orderData);

        return [
            'gateway'     => 'razorpay',
            'order_id'    => $razorpayOrder['id'],
            'amount'      => $orderData['amount'],
            'key'         => $this->key,
            'session_id'  => null,
            'environment' => null,
        ];
    }

    public function verifyPayment(array $data): array
    {
        try {
            $attributes = [
                'razorpay_order_id'   => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature'  => $data['razorpay_signature'],
            ];

            $this->api->utility->verifyPaymentSignature($attributes);

            return [
                'verified'   => true,
                'payment_id' => $data['razorpay_payment_id'],
            ];
        } catch (Exception $e) {
            return [
                'verified'   => false,
                'payment_id' => null,
                'error'      => $e->getMessage(),
            ];
        }
    }

    public function getGatewayName(): string
    {
        return 'razorpay';
    }

    public function getCheckoutScriptUrl(): string
    {
        return 'https://checkout.razorpay.com/v1/checkout.js';
    }
}
