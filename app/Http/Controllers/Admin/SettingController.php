<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
            'site_name' => Setting::get('site_name', 'BizGurukul'),
            'company_address' => Setting::get('company_address', '123 Business Park, Tech Hub'),
            'company_city' => Setting::get('company_city', 'New Delhi'),
            'company_state' => Setting::get('company_state', 'Delhi'),
            'company_zip' => Setting::get('company_zip', '110001'),
            'company_email' => Setting::get('company_email', 'support@bizgurukul.com'),
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
}
