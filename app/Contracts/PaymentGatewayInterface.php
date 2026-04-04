<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Create an order/session on the payment gateway.
     *
     * @param array $data [
     *   'amount'      => float,       // Amount in INR (not paise)
     *   'receipt'      => string,      // Unique receipt ID
     *   'currency'     => string,      // Default 'INR'
     *   'notes'        => array,       // Custom metadata
     *   'customer'     => array,       // ['name', 'email', 'phone']
     *   'return_url'   => string|null, // Cashfree redirect URL
     * ]
     * @return array [
     *   'gateway'        => string,
     *   'order_id'       => string,
     *   'amount'         => int,         // Amount in smallest unit (paise)
     *   'key'            => string|null, // Razorpay key / Cashfree app_id
     *   'session_id'     => string|null, // Cashfree session ID
     *   'environment'    => string|null, // Cashfree: 'sandbox' or 'production'
     * ]
     */
    public function createOrder(array $data): array;

    /**
     * Verify that a payment was successful.
     *
     * @param array $data Gateway-specific verification params
     * @return array ['verified' => bool, 'payment_id' => string|null]
     */
    public function verifyPayment(array $data): array;

    /**
     * Get the gateway identifier name.
     */
    public function getGatewayName(): string;

    /**
     * Get the frontend checkout JS SDK URL.
     */
    public function getCheckoutScriptUrl(): string;
}
