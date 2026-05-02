<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\RegistrationService;
use App\Services\Gateways\CashfreeGateway;
use App\Models\Payment;
use App\Models\Lead;
use Illuminate\Auth\Events\Registered;

class CashfreeWebhookController extends Controller
{
    protected RegistrationService $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $headers = $request->headers->all();

        Log::info('Cashfree Webhook Raw Debug:', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'headers' => $headers,
            'payload' => $payload
        ]);

        $timestamp = $request->header('x-webhook-timestamp', '');
        $signature = $request->header('x-webhook-signature', '');

        $data = json_decode($payload, true);

        // Handle empty or invalid JSON (often sent by test bots)
        if (!$data) {
            Log::info('Cashfree Webhook: Received empty or invalid payload');
            return response()->json(['status' => 'success'], 200);
        }

        $eventType = $data['type'] ?? '';
        Log::info('Cashfree Webhook Received', ['type' => $eventType]);

        // 1. Handle Test/Ping events IMMEDIATELY (Bypass signature for tests)
        if (in_array($eventType, ['PING', 'TEST', 'TEST_WEBHOOK']) || empty($eventType)) {
            return response()->json(['status' => 'success'], 200);
        }

        // 2. Verify Signature — MANDATORY for all real events (Security Fix)
        $cashfreeGateway = new CashfreeGateway();

        if (empty($signature)) {
            Log::warning('Cashfree Webhook: Missing signature header — rejecting request');
            return response()->json(['message' => 'Missing signature'], 401);
        }

        if (!$cashfreeGateway->verifyWebhookSignature($payload, $timestamp, $signature)) {
            Log::warning('Cashfree Webhook: Invalid Signature');
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // 3. Handle PAYMENT_SUCCESS events

        if (in_array($eventType, ['PAYMENT_SUCCESS_WEBHOOK', 'PAYMENT_SUCCESS'])) {
            $orderData = $data['data']['order'] ?? [];
            $paymentData = $data['data']['payment'] ?? [];

            $orderId = $orderData['order_id'] ?? null;
            $cfPaymentId = $paymentData['cf_payment_id'] ?? null;

            if (!$orderId) {
                Log::warning('Cashfree Webhook: Missing order_id');
                return response()->json(['status' => 'ignored'], 200);
            }

            // Check if this payment is already recorded
            $existingPayment = Payment::query()->where('gateway_order_id', $orderId)->first();

            if ($existingPayment && $existingPayment->status === 'success') {
                Log::info('Cashfree Webhook: Order already processed - ' . $orderId);
                return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
            }

            // If pending payment exists, update it to success but DON'T return yet —
            // we must also complete registration if it hasn't been done
            if ($existingPayment && $existingPayment->status !== 'success') {
                $existingPayment->fill([
                    'gateway_payment_id' => $cfPaymentId,
                    'status' => 'success',
                ])->save();
                Log::info('Cashfree Webhook: Updated existing payment for order ' . $orderId);
            }

            // Handle Registration Payments (lead-based)
            $orderTags = $orderData['order_tags'] ?? [];
            $orderNote = $orderData['order_note'] ?? '';

            // Try to extract lead_id from order tags or note
            $leadId = $orderTags['lead_id'] ?? null;

            if (!$leadId && $orderNote) {
                $decoded = json_decode($orderNote, true);
                $leadId = $decoded['lead_id'] ?? null;
            }

            if ($leadId) {
                $lead = Lead::query()->find($leadId);
                if (!$lead) {
                    Log::info('Cashfree Webhook: Lead not found (ID: ' . $leadId . ')');
                    return response()->json(['status' => 'success', 'message' => 'Lead already processed'], 200);
                }

                try {
                    $couponCode = $orderTags['coupon_code'] ?? null;
                    if (!$couponCode && $orderNote) {
                        $decoded = json_decode($orderNote, true);
                        $couponCode = $decoded['coupon_code'] ?? null;
                    }

                    $verificationData = [
                        'lead_id'             => $leadId,
                        'coupon_code'         => $couponCode,
                        'cashfree_order_id'   => $orderId,
                        'cashfree_payment_id' => $cfPaymentId,
                        'gateway'             => 'cashfree', // Explicit gateway — prevents wrong gateway fallback
                        'gateway_payment_id'  => $cfPaymentId,
                        'is_webhook'          => true,
                    ];

                    $user = $this->registrationService->verifyAndCompleteRegistration($verificationData);

                    event(new Registered($user));

                    Log::info('Cashfree Webhook: Successfully processed lead ' . $leadId);
                    return response()->json(['status' => 'success'], 200);
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    Log::info('Cashfree Webhook: Lead concurrently processed ' . $orderId);
                    return response()->json(['status' => 'success'], 200);
                } catch (\Exception $e) {
                    Log::error('Cashfree Webhook Process Error: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
                    return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
                }
            }

            // If we updated the payment but had no lead (e.g., student checkout), just respond success
            if ($existingPayment) {
                return response()->json(['status' => 'success'], 200);
            }

            Log::info('Cashfree Webhook: No matching handler for order ' . $orderId);
        }

        return response()->json(['status' => 'ignored'], 200);
    }
}
