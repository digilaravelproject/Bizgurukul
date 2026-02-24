<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\User;
use App\Models\Setting;
use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{
    /**
     * Show Referral Entry Step
     */
    public function showReferralStep()
    {
        $user = Auth::user();

        // 1. If user already has a referrer, skip this step
        if ($user->referred_by) {
            return $this->redirectAfterOnboarding();
        }

        // 2. Check for Cookie to Auto-fill (or Auto-submit if we want to be aggressive)
        $referralCode = Cookie::get('referral_code');

        return view('auth.onboarding-referral', compact('referralCode'));
    }

    /**
     * Store Referrer
     */
    public function storeReferrer(Request $request)
    {
        $request->validate([
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
        ]);

        $user = Auth::user();

        // Double check not already referred
        if ($user->referred_by) {
             return $this->redirectAfterOnboarding();
        }

        if ($request->filled('referral_code')) {
            try {
                DB::beginTransaction();

                $referrer = User::where('referral_code', $request->referral_code)->first();

                if ($referrer && $referrer->id !== $user->id) {

                    // Update User
                    $user->referred_by = $referrer->id;
                    $user->save();

                    // Create Commission (if active)
                    if (Setting::get('referral_system_active') === '1') {
                         $commAmount = (float) Setting::get('referral_commission_amount', 0);

                         if ($commAmount > 0) {
                            app(\App\Services\WalletService::class)->processCommission([
                                'affiliate_id' => $referrer->id,
                                'referred_user_id' => $user->id,
                                'amount' => $commAmount,
                                'notes' => 'Registration bonus commission (Onboarding).',
                            ]);
                         }
                    }

                    // Clear Cookie
                    Cookie::queue(Cookie::forget('referral_code'));
                }

                DB::commit();

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Onboarding Referral Error: " . $e->getMessage());
                return back()->with('error', 'Could not apply referral code. Please try again.');
            }
        }

        return $this->redirectAfterOnboarding();
    }

    /**
     * Skip Step
     */
    public function skip()
    {
        return $this->redirectAfterOnboarding();
    }

    /**
     * Helper: Redirect logic
     */
    private function redirectAfterOnboarding()
    {
        // 1. Check for Intended URL (e.g. they came from a Course Link)
        if (session()->has('url.intended')) {
            return redirect()->intended();
        }

        // 2. Default Dashboard
        return redirect()->route('dashboard');
    }
}
