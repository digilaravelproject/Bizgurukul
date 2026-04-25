<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;

class ManualGateway implements PaymentGatewayInterface
{
    public function createOrder(array $data): array
    {
        return [
            'gateway' => 'manual',
            'order_id' => 'MANUAL_' . uniqid(),
            'amount' => $data['amount'] * 100,
            'key' => null,
        ];
    }

    public function verifyPayment(array $data): array
    {
        return [
            'verified' => true,
            'payment_id' => $data['gateway_payment_id'] ?? 'MANUAL_PAY_' . uniqid(),
        ];
    }

    public function getGatewayName(): string
    {
        return 'manual';
    }

    public function getCheckoutScriptUrl(): string
    {
        return '';
    }
}
