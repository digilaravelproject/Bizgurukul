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
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $timestamp = $request->header('x-webhook-timestamp', '');
        $signature = $request->header('x-webhook-signature', '');

        // Verify webhook signature
        $cashfreeGateway = new CashfreeGateway();

        if ($signature && !$cashfreeGateway->verifyWebhookSignature($payload, $timestamp, $signature)) {
            Log::error('Cashfree Webhook Signature Verification Failed');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);

        Log::info('Cashfree Webhook Received', ['type' => $data['type'] ?? 'unknown']);

        // Handle PAYMENT_SUCCESS events
        $eventType = $data['type'] ?? '';

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
            $existingPayment = Payment::where('gateway_order_id', $orderId)->first();

            if ($existingPayment) {
                if ($existingPayment->status === 'success') {
                    Log::info('Cashfree Webhook: Order already processed - ' . $orderId);
                    return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
                }

                // Update existing pending payment to success
                $existingPayment->update([
                    'gateway_payment_id' => $cfPaymentId,
                    'status' => 'success',
                ]);

                Log::info('Cashfree Webhook: Updated existing payment for order ' . $orderId);
                return response()->json(['status' => 'success'], 200);
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
                $lead = Lead::find($leadId);
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
                        'lead_id'            => $leadId,
                        'coupon_code'        => $couponCode,
                        'cashfree_order_id'  => $orderId,
                        'cashfree_payment_id' => $cfPaymentId,
                        'is_webhook'         => true,
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

            Log::info('Cashfree Webhook: No matching handler for order ' . $orderId);
        }

        return response()->json(['status' => 'ignored'], 200);
    }
}
