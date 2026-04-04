<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\RegistrationService;
use App\Models\Payment;
use App\Models\Lead;
use Razorpay\Api\Api;
use Illuminate\Auth\Events\Registered;

class RazorpayWebhookController extends Controller
{
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');
        
        // Ensure you set this in your .env as RAZORPAY_WEBHOOK_SECRET and config/services.php
        $webhookSecret = config('services.razorpay.webhook_secret');
        
        if (!$webhookSecret) {
            Log::warning('Razorpay webhook secret not configured. Set RAZORPAY_WEBHOOK_SECRET.');
            return response()->json(['status' => 'ignored'], 200);
        }

        try {
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $api->utility->verifyWebhookSignature($payload, $signature, $webhookSecret);
        } catch (\Exception $e) {
            Log::error('Razorpay Webhook Signature Verification Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        $data = json_decode($payload, true);
        
        Log::info('Razorpay Webhook Received', ['event' => $data['event'] ?? 'unknown']);

        // Handle successful payment events
        if (isset($data['event']) && in_array($data['event'], ['payment.captured', 'order.paid'])) {
            $paymentEntity = $data['payload']['payment']['entity'] ?? null;
            $orderEntity = $data['payload']['order']['entity'] ?? null;
            
            if (!$paymentEntity) {
                return response()->json(['status' => 'ignored', 'message' => 'No payment entity found'], 200);
            }

            $orderId = $paymentEntity['order_id'];
            $paymentId = $paymentEntity['id'];
            
            // Check if already processed (Race condition prevention)
            if (Payment::where('razorpay_order_id', $orderId)->orWhere('gateway_order_id', $orderId)->exists()) {
                Log::info('Razorpay Webhook: Order already processed ' . $orderId);
                return response()->json(['status' => 'success', 'message' => 'Already processed'], 200);
            }

            // Extract notes correctly from order or payment entity
            $notes = collect($paymentEntity['notes'] ?? [])->merge($orderEntity['notes'] ?? [])->toArray();
            
            if (empty($notes['lead_id'])) {
                Log::warning('Razorpay Webhook: Missing lead_id in notes for order ' . $orderId);
                return response()->json(['status' => 'ignored', 'message' => 'Missing lead details'], 200);
            }

            // Check if lead exists
            $lead = Lead::find($notes['lead_id']);
            if (!$lead) {
                Log::info('Razorpay Webhook: Lead not found or already processed (Lead ID: ' . $notes['lead_id'] . ')');
                return response()->json(['status' => 'success', 'message' => 'Lead already processed'], 200);
            }

            try {
                // Prepare verification data bypassing user signature since this is a server-side verified webhook
                $verificationData = [
                    'lead_id' => $notes['lead_id'],
                    'coupon_code' => $notes['coupon_code'] ?? null,
                    'razorpay_order_id' => $orderId,
                    'razorpay_payment_id' => $paymentId,
                    'gateway' => 'razorpay',
                    'is_webhook' => true // Custom flag to bypass utility->verifyPaymentSignature
                ];
                
                $user = $this->registrationService->verifyAndCompleteRegistration($verificationData);
                
                // Fire Registered Event
                event(new Registered($user));
                
                Log::info('Razorpay Webhook: Successfully processed lead ' . $lead->id);
                return response()->json(['status' => 'success'], 200);
                
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                // Lead was just processed by the frontend in parallel
                Log::info('Razorpay Webhook: Lead concurrently processed ' . $orderId);
                return response()->json(['status' => 'success'], 200);
            } catch (\Exception $e) {
                Log::error('Razorpay Webhook Process Error: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
                return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
            }
        }

        return response()->json(['status' => 'ignored'], 200);
    }
}
