<?php

namespace App\Services\Gateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CashfreeGateway implements PaymentGatewayInterface
{
    protected string $appId;
    protected string $secretKey;
    protected string $environment;
    protected string $apiVersion;

    public function __construct()
    {
        // Admin panel settings override .env defaults
        $this->appId     = Setting::get('cashfree_app_id') ?: config('services.cashfree.app_id', '');
        $this->secretKey = Setting::get('cashfree_secret_key') ?: config('services.cashfree.secret_key', '');
        $this->environment = Setting::get('cashfree_environment') ?: config('services.cashfree.environment', 'sandbox');
        $this->apiVersion = '2023-08-01';
    }

    /**
     * Get the base URL based on environment.
     */
    protected function getBaseUrl(): string
    {
        return $this->environment === 'production'
            ? 'https://api.cashfree.com/pg'
            : 'https://sandbox.cashfree.com/pg';
    }

    /**
     * Get common HTTP headers for Cashfree API calls.
     */
    protected function getHeaders(): array
    {
        return [
            'x-client-id'     => $this->appId,
            'x-client-secret' => $this->secretKey,
            'x-api-version'   => $this->apiVersion,
            'Content-Type'    => 'application/json',
            'Accept'          => 'application/json',
        ];
    }

    public function createOrder(array $data): array
    {
        $orderId = 'cf_' . time() . '_' . rand(1000, 9999);
        $amountInRupees = round($data['amount'], 2);

        $payload = [
            'order_id'       => $orderId,
            'order_amount'   => $amountInRupees,
            'order_currency' => $data['currency'] ?? 'INR',
            'customer_details' => [
                'customer_id'    => 'cust_' . ($data['customer']['id'] ?? time()),
                'customer_name'  => $data['customer']['name'] ?? 'Customer',
                'customer_email' => $data['customer']['email'] ?? '',
                'customer_phone' => $data['customer']['phone'] ?? '',
            ],
            'order_meta' => [
                'return_url' => $data['return_url'] ?? url('/'),
            ],
        ];

        if (!empty($data['notes'])) {
            $payload['order_note'] = is_array($data['notes'])
                ? json_encode($data['notes'])
                : $data['notes'];
            $payload['order_tags'] = $data['notes'];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(30)
                ->post($this->getBaseUrl() . '/orders', $payload);

            if (!$response->successful()) {
                $errorBody = $response->json();
                $errorMsg = $errorBody['message'] ?? $response->body();
                Log::error('Cashfree CreateOrder Failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new Exception('Cashfree order creation failed: ' . $errorMsg);
            }

            $responseData = $response->json();

            return [
                'gateway'           => 'cashfree',
                'order_id'          => $responseData['order_id'] ?? $orderId,
                'amount'            => intval(round($amountInRupees * 100)),
                'key'               => $this->appId,
                'session_id'        => $responseData['payment_session_id'] ?? null,
                'environment'       => $this->environment,
                'cf_order_id'       => $responseData['cf_order_id'] ?? null,
            ];

        } catch (Exception $e) {
            Log::error('Cashfree CreateOrder Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    public function verifyPayment(array $data): array
    {
        try {
            $orderId = $data['order_id'];

            $response = Http::withHeaders($this->getHeaders())
                ->timeout(30)
                ->get($this->getBaseUrl() . '/orders/' . $orderId);

            if (!$response->successful()) {
                Log::error('Cashfree VerifyPayment Failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return ['verified' => false, 'payment_id' => null, 'error' => 'Failed to fetch order'];
            }

            $orderData = $response->json();
            $orderStatus = $orderData['order_status'] ?? '';

            if ($orderStatus === 'PAID') {
                // Get payment details
                $paymentResponse = Http::withHeaders($this->getHeaders())
                    ->timeout(30)
                    ->get($this->getBaseUrl() . '/orders/' . $orderId . '/payments');

                $payments = $paymentResponse->json();
                $cfPaymentId = null;

                if (is_array($payments) && !empty($payments)) {
                    foreach ($payments as $payment) {
                        if (($payment['payment_status'] ?? '') === 'SUCCESS') {
                            $cfPaymentId = $payment['cf_payment_id'] ?? null;
                            break;
                        }
                    }
                }

                return [
                    'verified'   => true,
                    'payment_id' => $cfPaymentId ?? ('cf_pay_' . $orderId),
                ];
            }

            return [
                'verified'   => false,
                'payment_id' => null,
                'error'      => 'Order status: ' . $orderStatus,
            ];

        } catch (Exception $e) {
            Log::error('Cashfree VerifyPayment Exception: ' . $e->getMessage());
            return ['verified' => false, 'payment_id' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify webhook signature from Cashfree.
     */
    public function verifyWebhookSignature(string $payload, string $timestamp, string $signature): bool
    {
        $webhookSecret = Setting::get('cashfree_webhook_secret') ?: config('services.cashfree.webhook_secret', '');

        if (empty($webhookSecret)) {
            Log::warning('Cashfree webhook secret not configured.');
            return false;
        }

        $signatureData = $timestamp . $payload;
        $computedSignature = base64_encode(hash_hmac('sha256', $signatureData, $webhookSecret, true));

        return hash_equals($computedSignature, $signature);
    }

    public function getGatewayName(): string
    {
        return 'cashfree';
    }

    public function getCheckoutScriptUrl(): string
    {
        return 'https://sdk.cashfree.com/js/v3/cashfree.js';
    }

    /**
     * Test connection to Cashfree API.
     */
    public function testConnection(): array
    {
        try {
            // Use a simple order creation with a minimal payload to test credentials
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(15)
                ->post($this->getBaseUrl() . '/orders', [
                    'order_id'       => 'test_' . time(),
                    'order_amount'   => 1.00,
                    'order_currency' => 'INR',
                    'customer_details' => [
                        'customer_id'    => 'test_customer',
                        'customer_name'  => 'Test',
                        'customer_email' => 'test@test.com',
                        'customer_phone' => '9999999999',
                    ],
                ]);

            if ($response->successful()) {
                return ['success' => true, 'message' => 'Connection successful! (' . ucfirst($this->environment) . ' mode)'];
            }

            $error = $response->json();
            return ['success' => false, 'message' => $error['message'] ?? 'Authentication failed. Check your API keys.'];

        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Connection error: ' . $e->getMessage()];
        }
    }
}
