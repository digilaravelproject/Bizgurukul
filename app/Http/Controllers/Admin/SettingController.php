<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
}
