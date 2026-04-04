<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Setting;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * Create a payment gateway instance based on the active setting.
     *
     * @param string|null $gateway Force a specific gateway (override admin setting)
     * @return PaymentGatewayInterface
     */
    public static function make(?string $gateway = null): PaymentGatewayInterface
    {
        $gateway = $gateway ?? Setting::get('active_payment_gateway', 'razorpay');

        return match ($gateway) {
            'cashfree' => new CashfreeGateway(),
            'razorpay' => new RazorpayGateway(),
            default    => throw new InvalidArgumentException("Unsupported payment gateway: {$gateway}"),
        };
    }

    /**
     * Get the currently active gateway name from settings.
     */
    public static function activeGateway(): string
    {
        return Setting::get('active_payment_gateway', 'razorpay');
    }

    /**
     * Check if a specific gateway is active.
     */
    public static function isActive(string $gateway): bool
    {
        return self::activeGateway() === $gateway;
    }
}
