<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\User;
use App\Models\AffiliateCommission;
use App\Models\Tax;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Razorpay\Api\Api;

class RegistrationFlowController extends Controller
{
    protected $registrationService;

    public function __construct(\App\Services\RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }
    /**
     * Phase 1: Show Lead Capture Form
     */
    public function showPhase1(Request $request)
    {
        return view('auth.register-phase1', ['email' => $request->query('email')]);
    }

    /**
     * Phase 1: Store Lead Data
     */
    public function storePhase1(Request $request)
    {
        try {
            $request->validate([
                'name'     => ['required', 'string', 'max:255'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'mobile'   => ['required', 'numeric', 'digits_between:10,15'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'gender'   => ['nullable', 'string'],
                'dob'      => ['nullable', 'date'],
                'state'    => ['nullable', 'string'],
                'pincode'  => ['nullable', 'numeric', 'digits:6'],
            ]);

            $lead = Lead::updateOrCreate(
                ['email' => $request->email],
                [
                    'name'       => $request->name,
                    'mobile'     => $request->mobile,
                    'password'   => Hash::make($request->password), // Hashed here
                    'gender'     => $request->gender,
                    'dob'        => $request->dob,
                    'state'      => $request->state,
                    'pincode'    => $request->pincode,
                    'ip_address' => $request->ip(),
                    'referral_code' => Cookie::get('referral_code') ?: session('referral_code'),
                ]
            );

            return redirect()->route('register.phase2', ['lead_id' => $lead->id]);

        } catch (\Exception $e) {
            Log::error('Phase 1 Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Unable to save details. Please try again.']);
        }
    }

    /**
     * Phase 2: Product Selection & Sponsor Verification
     */
    public function showPhase2(Request $request)
    {
        try {
            $lead = Lead::findOrFail($request->query('lead_id'));

            $referralCode = $lead->referral_code ?? (session('referral_code') ?? Cookie::get('referral_code'));
            $sponsor = $referralCode ? User::where('referral_code', $referralCode)->first() : null;

            // Affiliate link logic
            $bundles = Bundle::where('is_active', true)->ordered()->get();
            $affiliateLinkSlug = session('affiliate_link_slug');

            if ($affiliateLinkSlug) {
                $affiliateLink = \App\Models\AffiliateLink::where('slug', $affiliateLinkSlug)->first();
                if ($affiliateLink && $affiliateLink->target_type === 'specific_bundle') {
                    $specificBundle = Bundle::find($affiliateLink->target_id);
                    if ($specificBundle && $specificBundle->is_active) {
                        $bundles = collect([$specificBundle]);
                    }
                }
            }

            return view('auth.register-phase2', compact('lead', 'sponsor', 'maskedSponsor', 'bundles', 'referralCode'));

        } catch (\Exception $e) {
            Log::error('Phase 2 Error: ' . $e->getMessage());
            return redirect()->route('register.phase1')->withErrors('Session expired. Please start over.');
        }
    }

    /**
     * AJAX: Check Referral Code details
     */
    public function checkReferralPhase2(Request $request)
    {
        try {
            $sponsor = User::where('referral_code', $request->code)->first();

            if ($sponsor) {
                return response()->json([
                    'status' => 'valid',
                    'name'   => $this->maskString($sponsor->name),
                    'mobile' => $this->maskString($sponsor->mobile, 'mobile'),
                ]);
            }

            return response()->json(['status' => 'invalid']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Validation failed']);
        }
    }

    /**
     * Phase 2: Store Product Selection
     */
    public function storePhase2(Request $request)
    {
        try {
            $request->validate([
                'lead_id'       => 'required|exists:leads,id',
                'bundle_id'     => 'required|exists:bundles,id',
                'referral_code' => 'nullable|string'
            ]);

            $lead = Lead::findOrFail($request->lead_id);

            $lead->update([
                'product_preference' => ['bundle_id' => $request->bundle_id],
                'referral_code'      => $request->referral_code
            ]);

            return redirect()->route('register.phase3', ['lead_id' => $lead->id]);

        } catch (\Exception $e) {
            Log::error('Store Phase 2 Error: ' . $e->getMessage());
            return back()->withErrors('Unable to proceed. Please try again.');
        }
    }

    /**
     * Phase 3: Order Summary
     */
    public function showPhase3(Request $request)
    {
        try {
            $lead = Lead::findOrFail($request->query('lead_id'));

            if (empty($lead->product_preference['bundle_id'])) {
                return redirect()->route('register.phase2', ['lead_id' => $lead->id]);
            }

            $bundle = Bundle::findOrFail($lead->product_preference['bundle_id']);

            $hasReferral = !empty($lead->referral_code) || !empty(session('referral_code')) || !empty(Cookie::get('referral_code'));

            // Centralized Pricing Logic
            $pricing = $this->registrationService->calculatePricing($bundle, null, $hasReferral);

            $maskedEmail = $this->maskString($lead->email, 'email');

            return view('auth.register-phase3', array_merge(
                compact('lead', 'bundle', 'maskedEmail'),
                $pricing
            ));

        } catch (\Exception $e) {
            Log::error('Phase 3 Error: ' . $e->getMessage());
            return redirect()->route('register.phase2', ['lead_id' => $request->lead_id]);
        }
    }

    /**
     * AJAX: Check Coupon Logic
     */
    public function checkCoupon(Request $request)
    {
        try {
            $request->validate([
                'code'      => 'required|string',
                'bundle_id' => 'required|exists:bundles,id'
            ]);

            $bundle = Bundle::findOrFail($request->bundle_id);
            $hasReferral = !empty(session('referral_code')) || !empty(Cookie::get('referral_code'));
            $pricing = $this->registrationService->calculatePricing($bundle, $request->code, $hasReferral);

            if (isset($pricing['error'])) {
                return response()->json(['status' => 'invalid', 'message' => $pricing['error']]);
            }

            return response()->json([
                'status'         => 'valid',
                'message'        => 'Coupon Applied Successfully',
                'discount'       => $pricing['discount'],
                'tax'            => $pricing['taxAmount'],
                'total'          => $pricing['totalAmount'],
                'taxable_amount' => $pricing['taxableAmount']
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage() ?: 'Error applying coupon']);
        }
    }

    /**
     * Razorpay: Initiate Payment (Create Order)
     */
    public function initiatePayment(Request $request)
    {
        try {
            $request->validate([
                'lead_id'     => 'required|exists:leads,id',
                'coupon_code' => 'nullable|string'
            ]);

            $lead = Lead::findOrFail($request->lead_id);
            $bundle = Bundle::findOrFail($lead->product_preference['bundle_id']);

            $hasReferral = !empty($lead->referral_code) || !empty(session('referral_code')) || !empty(Cookie::get('referral_code'));

            // Recalculate Price Securely
            $pricing = $this->registrationService->calculatePricing($bundle, $request->coupon_code, $hasReferral);

            // Initialize Razorpay
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            $orderData = [
                'receipt'         => 'rcpt_' . $lead->id . '_' . time(),
                'amount'          => round($pricing['totalAmount'] * 100), // Paise
                'currency'        => 'INR',
                'payment_capture' => 1
            ];

            $razorpayOrder = $api->order->create($orderData);

            return response()->json([
                'status'      => 'success',
                'order_id'    => $razorpayOrder['id'],
                'amount'      => $orderData['amount'],
                'key'         => config('services.razorpay.key'),
                'name'        => config('app.name'),
                'description' => $bundle->title,
                'prefill'     => [
                    'name'    => $lead->name,
                    'email'   => $lead->email,
                    'contact' => $lead->mobile
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Initiation Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Unable to initiate payment']);
        }
    }

    /**
     * Razorpay: Verify Payment & Activate Account
     */
    public function verifyPayment(Request $request)
    {
        try {
            $request->validate([
                'razorpay_order_id'   => 'required',
                'razorpay_payment_id' => 'required',
                'razorpay_signature'  => 'required',
                'lead_id'             => 'required|exists:leads,id',
                'coupon_code'         => 'nullable|string'
            ]);

            $user = $this->registrationService->verifyAndCompleteRegistration($request->all());

            // Fire Registered Event
            event(new Registered($user));

            // Authenticate and Regenerate Session
            Auth::login($user);
            $request->session()->regenerate();

            // Multi-Role Redirection Logic
            $redirectUrl = route('student.dashboard');
            if ($user->hasRole('Admin')) {
                $redirectUrl = route('admin.dashboard');
            }

            return response()->json([
                'status'       => 'success',
                'redirect_url' => $redirectUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Verification Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Payment verification failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper: Mask Strings (Mobile/Email/Name)
     */
    private function maskString($string, $type = 'text')
    {
        if (empty($string)) return '';

        if ($type === 'email') {
            $parts = explode('@', $string);
            if (count($parts) !== 2) return $string;

            $name = $parts[0];
            $domain = $parts[1];

            $maskedName = strlen($name) > 2
                ? $name[0] . str_repeat('*', strlen($name) - 2) . $name[strlen($name) - 1]
                : $name . '***';

            return $maskedName . '@' . $domain;
        }

        // Default: Mobile or Name
        return strlen($string) > 2
            ? $string[0] . str_repeat('*', strlen($string) - 2) . $string[strlen($string) - 1]
            : $string;
    }
}
