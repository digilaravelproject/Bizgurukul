<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Setting;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    /**
     * Show the billing settings form.
     */
    public function billing()
    {
        $settings = [
            'site_name' => Setting::get('site_name', 'Skills Pehle'),
            'company_address' => Setting::get('company_address', '123 Business Park, Tech Hub'),
            'company_city' => Setting::get('company_city', 'New Delhi'),
            'company_state' => Setting::get('company_state', 'Delhi'),
            'company_zip' => Setting::get('company_zip', '110001'),
            'company_email' => Setting::get('company_email', 'support@Skills Pehle.com'),
            'company_phone' => Setting::get('company_phone', '+91 9876543210'),
            'company_logo' => Setting::get('company_logo', null),
        ];

        return view('admin.settings.billing', compact('settings'));
    }

    /**
     * Update the billing settings.
     */
    public function updateBilling(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'company_city' => 'required|string|max:100',
            'company_state' => 'required|string|max:100',
            'company_zip' => 'required|string|max:20',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'required|string|max:30',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        try {
            $data = $request->except(['_token', 'company_logo']);

            foreach ($data as $key => $value) {
                Setting::set($key, $value);
            }

            // Handle Logo Upload
            if ($request->hasFile('company_logo')) {
                // Delete old logo if exists
                if ($oldLogo = Setting::get('company_logo')) {
                    Storage::disk('public')->delete($oldLogo);
                }

                $path = $request->file('company_logo')->store('site_images', 'public');
                Setting::set('company_logo', $path);
            }

            return redirect()->route('admin.settings.billing')->with('success', 'Billing Settings updated successfully.');

        } catch (Exception $e) {
            Log::error("Error updating billing settings: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update settings. Please try again.');
        }
    }

    // ──────────────────────────────────────
    // Email Configuration
    // ──────────────────────────────────────

    public function emailConfig()
    {
        $settings = [
            'mail_host'               => Setting::get('mail_host', ''),
            'mail_port'               => Setting::get('mail_port', '587'),
            'mail_username'           => Setting::get('mail_username', ''),
            'mail_password'           => Setting::get('mail_password', ''),
            'mail_encryption'         => Setting::get('mail_encryption', 'tls'),
            'mail_from_address'       => Setting::get('mail_from_address', ''),
            'mail_from_name'          => Setting::get('mail_from_name', Setting::get('site_name', config('app.name'))),
            'admin_notification_email'=> Setting::get('admin_notification_email', ''),
        ];

        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmailConfig(Request $request)
    {
        $request->validate([
            'mail_host'                => 'required|string|max:255',
            'mail_port'                => 'required|numeric',
            'mail_username'            => 'required|email|max:255',
            'mail_password'            => 'nullable|string|max:255',
            'mail_encryption'          => 'required|in:ssl,tls,none',
            'mail_from_address'        => 'required|email|max:255',
            'mail_from_name'           => 'required|string|max:255',
            'admin_notification_email' => 'required|email|max:255',
        ]);

        try {
            $keys = [
                'mail_host', 'mail_port', 'mail_username',
                'mail_encryption', 'mail_from_address', 'mail_from_name',
                'admin_notification_email',
            ];

            foreach ($keys as $key) {
                Setting::set($key, $request->input($key));
            }

            // Only update password if provided (don't blank out existing)
            if ($request->filled('mail_password')) {
                Setting::set('mail_password', $request->input('mail_password'));
            }

            return redirect()->route('admin.settings.email')
                ->with('success', 'Email configuration saved successfully.');

        } catch (Exception $e) {
            Log::error('Email config update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to save email settings.');
        }
    }

    public function testEmail(Request $request)
    {
        $request->validate(['test_email' => 'required|email']);

        try {
            EmailService::sendTest($request->input('test_email'));
            return response()->json(['success' => true, 'message' => 'Test email sent successfully! Please check your inbox.']);
        } catch (\Throwable $e) {
            Log::error('Test email error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 500);
        }
    }

    // ──────────────────────────────────────
    // Wallet Settings
    // ──────────────────────────────────────

    public function wallet()
    {
        $settings = [
            'commission_holding_hours'   => Setting::get('commission_holding_hours', 24),
            'tds_enabled'                => (bool) Setting::get('tds_enabled', true),
        ];

        return view('admin.settings.wallet', compact('settings'));
    }

    public function updateWallet(Request $request)
    {
        $request->validate([
            'commission_holding_hours'   => 'required|numeric|min:0',
            'tds_enabled'                => 'boolean',
        ]);

        try {
            Setting::set('commission_holding_hours', $request->input('commission_holding_hours'));
            Setting::set('tds_enabled', $request->boolean('tds_enabled'));

            // Refresh existing data based on the new setting
            app(\App\Services\WalletService::class)->recalculateHoldPeriod();

            return redirect()->route('admin.settings.wallet')
                ->with('success', 'Wallet settings updated successfully.');

        } catch (Exception $e) {
            Log::error('Wallet config update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to save wallet settings.');
        }
    }

    // ──────────────────────────────────────
    // Payment Gateway Configuration
    // ──────────────────────────────────────

    public function paymentGateway()
    {
        $settings = [
            'active_payment_gateway' => Setting::get('active_payment_gateway', 'razorpay'),

            // Razorpay
            'razorpay_key'            => Setting::get('razorpay_key', config('services.razorpay.key', '')),
            'razorpay_secret'         => Setting::get('razorpay_secret', config('services.razorpay.secret', '')),
            'razorpay_webhook_secret' => Setting::get('razorpay_webhook_secret', config('services.razorpay.webhook_secret', '')),

            // Cashfree
            'cashfree_app_id'         => Setting::get('cashfree_app_id', config('services.cashfree.app_id', '')),
            'cashfree_secret_key'     => Setting::get('cashfree_secret_key', config('services.cashfree.secret_key', '')),
            'cashfree_webhook_secret' => Setting::get('cashfree_webhook_secret', config('services.cashfree.webhook_secret', '')),
            'cashfree_environment'    => Setting::get('cashfree_environment', config('services.cashfree.environment', 'sandbox')),
        ];

        // Generate webhook URLs for display
        $webhookUrls = [
            'razorpay' => route('webhook.razorpay'),
            'cashfree' => url('/webhook/cashfree'),
        ];

        return view('admin.settings.payment', compact('settings', 'webhookUrls'));
    }

    public function updatePaymentGateway(Request $request)
    {
        $request->validate([
            'active_payment_gateway' => 'required|in:razorpay,cashfree',

            // Razorpay fields
            'razorpay_key'            => 'nullable|string|max:255',
            'razorpay_secret'         => 'nullable|string|max:255',
            'razorpay_webhook_secret' => 'nullable|string|max:255',

            // Cashfree fields
            'cashfree_app_id'         => 'nullable|string|max:255',
            'cashfree_secret_key'     => 'nullable|string|max:255',
            'cashfree_webhook_secret' => 'nullable|string|max:255',
            'cashfree_environment'    => 'nullable|in:sandbox,production',
        ]);

        try {
            $keys = [
                'active_payment_gateway',
                'razorpay_key', 'razorpay_secret', 'razorpay_webhook_secret',
                'cashfree_app_id', 'cashfree_secret_key', 'cashfree_webhook_secret', 'cashfree_environment',
            ];

            foreach ($keys as $key) {
                $value = $request->input($key);
                if ($value !== null && $value !== '') {
                    Setting::set($key, $value);
                }
            }

            return redirect()->route('admin.settings.payment')
                ->with('success', 'Payment gateway settings updated successfully.');

        } catch (Exception $e) {
            Log::error('Payment gateway config update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to save payment gateway settings.');
        }
    }

    public function testPaymentGateway(Request $request)
    {
        $gateway = $request->input('gateway', 'razorpay');

        try {
            if ($gateway === 'cashfree') {
                $gw = new \App\Services\Gateways\CashfreeGateway();
                $result = $gw->testConnection();
                return response()->json($result);
            }

            // Razorpay test: try to fetch auth
            $key = Setting::get('razorpay_key') ?: config('services.razorpay.key');
            $secret = Setting::get('razorpay_secret') ?: config('services.razorpay.secret');

            if (empty($key) || empty($secret)) {
                return response()->json(['success' => false, 'message' => 'Razorpay API keys are not configured.']);
            }

            $api = new \Razorpay\Api\Api($key, $secret);
            // Fetch a dummy order list to verify credentials
            $api->order->all(['count' => 1]);

            return response()->json(['success' => true, 'message' => 'Razorpay connection successful!']);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Test payment gateway webhook setup.
     */
    public function testWebhook(Request $request)
    {
        $gateway = $request->input('gateway', 'razorpay');
        $webhookUrl = $gateway === 'razorpay' ? route('webhook.razorpay') : url('/webhook/cashfree');

        // Get the secret being tested (from input if provided, else from setting)
        $secret = $request->input('webhook_secret');
        if (!$secret) {
            $secret = $gateway === 'razorpay' 
                ? Setting::get('razorpay_webhook_secret', config('services.razorpay.webhook_secret'))
                : Setting::get('cashfree_webhook_secret', config('services.cashfree.webhook_secret'));
        }

        if (empty($secret)) {
            return response()->json(['success' => false, 'message' => 'Webhook secret is not configured.']);
        }

        try {
            $payload = json_encode([
                'type' => 'TEST_WEBHOOK',
                'event' => 'test.webhook',
                'created_at' => now()->timestamp,
                'data' => ['message' => 'This is a test webhook from Admin Panel']
            ]);

            $headers = [];
            if ($gateway === 'razorpay') {
                $headers['X-Razorpay-Signature'] = hash_hmac('sha256', $payload, $secret);
            } else {
                $timestamp = (string) round(microtime(true) * 1000);
                $headers['x-webhook-timestamp'] = $timestamp;
                $headers['x-webhook-signature'] = base64_encode(hash_hmac('sha256', $timestamp . $payload, $secret, true));
            }

            // Perform internal HTTP request to test the webhook endpoint
            $response = Http::withHeaders($headers)
                ->withBody($payload, 'application/json')
                ->timeout(10)
                ->post($webhookUrl);

            // Our webhook controllers return 200 for successful (or ignored but verified) events.
            // They return 400 for signature verification failures.
            if ($response->successful()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Webhook reached successfully and secret verified!'
                ]);
            }

            if ($response->status() === 400) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Signature verification failed. Please check your Webhook Secret.'
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Webhook test failed. Server returned status: ' . $response->status()
            ]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Connection error: ' . $e->getMessage()]);
        }
    }
}
