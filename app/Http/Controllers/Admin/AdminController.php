<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AffiliateCommission;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Admin Dashboard: Financial Stats and Global Settings
     */
    public function dashboard()
    {
        try {
            // 1. Stats Calculation (Using Scopes from Model)
            $totalStudents = User::role('Student')->count();

            // Decimal values ke liye sum safely handle karna
            $totalCommissionsPaid = (float) AffiliateCommission::paid()->sum('amount');
            $pendingCommissions = (float) AffiliateCommission::pending()->sum('amount');

            // 2. Settings (Model ke get() method se Cache auto-handle ho raha hai)
            $settings = [
                'referral_system_active' => Setting::get('referral_system_active', '0'),
                'referral_commission_amount' => Setting::get('referral_commission_amount', '0'),
                'referral_cookie_expiry_days' => Setting::get('referral_cookie_expiry_days', '30'),
            ];

            return view('admin.dashboard', compact(
                'totalStudents',
                'totalCommissionsPaid',
                'pendingCommissions',
                'settings'
            ));

        } catch (Exception $e) {
            Log::error("Admin Dashboard View Error: " . $e->getMessage());
            return back()->with('error', 'Failed to load dashboard statistics.');
        }
    }

    /**
     * Update Global Affiliate Settings
     */
    public function updateSettings(Request $request)
    {
        // 1. Validation with strict types
        $validated = $request->validate([
            'referral_commission_amount' => 'required|numeric|min:0',
            'referral_cookie_expiry_days' => 'required|integer|min:1|max:365',
        ]);

        DB::beginTransaction();
        try {
            // 2. Toggle logic: Checkbox data clean karna
            $isActive = $request->boolean('referral_system_active') ? '1' : '0';

            // 3. Update Settings (Hamara Model auto-cache clear karega)
            Setting::set('referral_system_active', $isActive);
            Setting::set('referral_commission_amount', $validated['referral_commission_amount']);
            Setting::set('referral_cookie_expiry_days', $validated['referral_cookie_expiry_days']);

            DB::commit();

            // Log activity for audit trail
            Log::info("Admin ID " . Auth::id() . " updated affiliate settings.");

            return back()->with('success', 'Affiliate Settings Updated Successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Settings Update Error: " . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to update settings. Please try again.');
        }
    }
}
