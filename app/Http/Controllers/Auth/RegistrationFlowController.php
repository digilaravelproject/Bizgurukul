<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\State;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Razorpay\Api\Api;

class RegistrationFlowController extends Controller
{
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * Phase 1: Show Lead Capture Form
     */
    public function showPhase1(Request $request)
    {
        $states = State::orderBy('name')->get();

        return view('auth.register-phase1', [
            'email' => $request->query('email'),
            'states' => $states,
            'intent' => $request->query('intent'),
            'target_bundle_id' => $request->query('id'),
        ]);
    }

    /**
     * Phase 1: Store Lead Data
     */
    public function storePhase1(Request $request)
    {
        try {
            // Clean mobile number (remove spaces, dots, hyphens, plus)
            if ($request->has('mobile')) {
                $request->merge([
                    'mobile' => preg_replace('/[^0-9]/', '', $request->mobile),
                ]);
            }

            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'mobile' => ['required', 'numeric', 'digits_between:10,15'],
                'password' => ['required', Password::defaults()],
                'gender' => ['nullable', 'string'],
                'state_id' => ['nullable', 'exists:states,id'],
                'intent' => ['nullable', 'string'],
                'target_bundle_id' => ['nullable'], // Validated manually below to handle course/bundle mix
            ]);

            $productPreference = null;
            if ($request->intent === 'bundle' && $request->target_bundle_id) {
                if (Bundle::where('id', $request->target_bundle_id)->exists()) {
                    $productPreference = ['bundle_id' => $request->target_bundle_id];
                }
            } elseif ($request->intent === 'course' && $request->target_bundle_id) {
                /** @var Course|null $course */
                $course = Course::find($request->target_bundle_id);
                if ($course) {
                    $bundle = $course->bundles()->first();
                    if ($bundle) {
                        $productPreference = ['bundle_id' => $bundle->id];
                    }
                }
            }

            // Sanitize name for database safety (remove 4-byte UTF-8 chars like emojis if DB charset is utf8)
            $sanitizedName = preg_replace('/[^\p{L}\p{N}\s\p{P}]/u', '', $request->name) ?: $request->name;

            $lead = Lead::updateOrCreate(
                ['email' => $request->email],
                [
                    'name' => $sanitizedName,
                    'mobile' => $request->mobile,
                    'password' => Hash::make($request->password), // Hashed here
                    'gender' => $request->gender,
                    'state_id' => $request->state_id,
                    'ip_address' => $request->ip(),
                    'product_preference' => $productPreference,
                    'referral_code' => Cookie::get('referral_code') ?: session('referral_code'),
                ]
            );

            return redirect()->route('register.phase2', ['lead_id' => $lead->id]);

        } catch (ValidationException $e) {
            Log::warning('Phase 1 Validation Error: ', $e->errors());

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Phase 1 General Error: '.$e->getMessage(), [
                'stack' => $e->getTraceAsString(),
            ]);

            return back()->withErrors(['error' => 'Unable to save details: '.$e->getMessage()])->withInput();
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

            // Masked Sponsor for UI (consistent with AlpineJS expectations)
            $maskedSponsor = null;
            if ($sponsor) {
                $maskedSponsor = (object) [
                    'name' => $sponsor->name,
                    'mobile' => $this->maskString($sponsor->mobile, 'mobile'),
                ];
            }

            // Affiliate link logic

            $bundles = Bundle::with(['courses' => function ($q) {
                $q->where('is_published', 1);
            }])->where('is_active', true)->where('is_published', 1)->ordered()->get();

            // Filter if lead has a preference
            if (! empty($lead->product_preference['bundle_id'])) {
                $preferredBundle = Bundle::find($lead->product_preference['bundle_id']);
                if ($preferredBundle && $preferredBundle->is_active && $preferredBundle->is_published) {
                    $bundles = collect([$preferredBundle]);
                }
            }

            $affiliateLinkSlug = session('affiliate_link_slug');

            if ($affiliateLinkSlug) {
                $affiliateLink = AffiliateLink::where('slug', $affiliateLinkSlug)->first();
                if ($affiliateLink && $affiliateLink->target_type === 'specific_bundle') {
                    $specificBundle = Bundle::find($affiliateLink->target_id);
                    if ($specificBundle && $specificBundle->is_active && $specificBundle->is_published) {
                        $bundles = collect([$specificBundle]);
                    }
                }
            }

            return view('auth.register-phase2', compact('lead', 'sponsor', 'maskedSponsor', 'bundles', 'referralCode'));

        } catch (\Exception $e) {
            Log::error('Phase 2 Error: '.$e->getMessage());

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
                    'name' => $sponsor->name,
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
                'lead_id' => 'required|exists:leads,id',
                'bundle_id' => 'required|exists:bundles,id',
                'referral_code' => 'nullable|string',
            ]);

            $lead = Lead::findOrFail($request->lead_id);

            $lead->update([
                'product_preference' => ['bundle_id' => $request->bundle_id],
                'referral_code' => $request->referral_code,
            ]);

            return redirect()->route('register.phase3', ['lead_id' => $lead->id]);

        } catch (\Exception $e) {
            Log::error('Store Phase 2 Error: '.$e->getMessage());

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

            $hasReferral = ! empty($lead->referral_code) || ! empty(session('referral_code')) || ! empty(Cookie::get('referral_code'));

            // Centralized Pricing Logic
            $pricing = $this->registrationService->calculatePricing($bundle, null, $hasReferral);

            $maskedEmail = $this->maskString($lead->email, 'email');

            return view('auth.register-phase3', array_merge(
                compact('lead', 'bundle', 'maskedEmail'),
                $pricing
            ));

        } catch (\Exception $e) {
            Log::error('Phase 3 Error: '.$e->getMessage());

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
                'code' => 'required|string',
                'bundle_id' => 'required|exists:bundles,id',
                'lead_id' => 'required|exists:leads,id',
            ]);

            $bundle = Bundle::findOrFail($request->bundle_id);
            $lead = Lead::findOrFail($request->lead_id);

            $hasReferral = ! empty($lead->referral_code) || ! empty(session('referral_code')) || ! empty(Cookie::get('referral_code'));
            $pricing = $this->registrationService->calculatePricing($bundle, $request->code, $hasReferral);

            if (isset($pricing['error'])) {
                return response()->json(['status' => 'invalid', 'message' => $pricing['error']]);
            }

            return response()->json([
                'status' => 'valid',
                'message' => 'Coupon Applied Successfully',
                'discount' => $pricing['discount'],
                'tax' => $pricing['taxAmount'],
                'taxes' => $pricing['taxes'], // Added to update frontend inclusive/exclusive display
                'total' => $pricing['totalAmount'],
                'taxable_amount' => $pricing['taxableAmount'],
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
                'lead_id' => 'required|exists:leads,id',
                'coupon_code' => 'nullable|string',
            ]);

            $lead = Lead::findOrFail($request->lead_id);
            $bundle = Bundle::findOrFail($lead->product_preference['bundle_id']);

            $hasReferral = ! empty($lead->referral_code) || ! empty(session('referral_code')) || ! empty(Cookie::get('referral_code'));

            // Recalculate Price Securely
            $pricing = $this->registrationService->calculatePricing($bundle, $request->coupon_code, $hasReferral);

            // Initialize Gateway via Factory
            $gateway = \App\Services\Gateways\PaymentGatewayFactory::make();
            $gatewayName = \App\Services\Gateways\PaymentGatewayFactory::activeGateway();

            $orderData = [
                'receipt' => 'rcpt_'.$lead->id.'_'.time(),
                'amount' => $pricing['totalAmount'],
                'currency' => 'INR',
                'notes' => [
                    'lead_id' => $lead->id,
                    'coupon_code' => $request->coupon_code ?? '',
                ],
            ];

            $orderResult = $gateway->createOrder($orderData);

            $response = [
                'status' => 'success',
                'gateway' => $gatewayName,
                'order_id' => $orderResult['order_id'],
                'amount' => $orderResult['amount'],
                'key' => $gatewayName === 'razorpay' ? config('services.razorpay.key') : null,
                'name' => config('app.name'),
                'description' => $bundle->title,
                'prefill' => [
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'contact' => $lead->mobile,
                ],
            ];

            if ($gatewayName === 'cashfree') {
                $response['session_id'] = $orderResult['session_id'] ?? null;
                $response['environment'] = $orderResult['environment'] ?? 'sandbox';
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Payment Initiation Failed: '.$e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Unable to initiate payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Razorpay: Verify Payment & Activate Account
     */
    public function verifyPayment(Request $request)
    {
        try {
            $request->validate([
                'gateway' => 'nullable|string',
                'razorpay_order_id' => 'required_without:cashfree_order_id',
                'cashfree_order_id' => 'required_without:razorpay_order_id',
                'lead_id' => 'required',
                'coupon_code' => 'nullable|string',
            ]);

            $user = null;

            try {
                // Try to complete registration normally
                $user = $this->registrationService->verifyAndCompleteRegistration($request->all());

                // Fire Registered Event (Only if we just created the user)
                event(new Registered($user));

            } catch (\Exception $e) {
                // If it failed, check if it was because the lead was already processed (e.g., by a webhook)
                $orderId = $request->razorpay_order_id ?? $request->cashfree_order_id;
                $payment = Payment::where('gateway_order_id', $orderId)
                                    ->orWhere('razorpay_order_id', $orderId)
                                    ->first();

                if ($payment && $payment->user) {
                    $user = $payment->user;
                    Log::info('RegistrationFlow: Lead already processed by parallel process. Resuming session for user: '.$user->id);
                } else {
                    // It's a genuine failure
                    Log::error('Payment Verification Failed: '.$e->getMessage());

                    return response()->json([
                        'status' => 'error',
                        'message' => 'Payment verification failed: '.$e->getMessage(),
                    ]);
                }
            }

            // Authenticate and Regenerate Session (Auto-login)
            Auth::login($user);
            $request->session()->regenerate();

            // Multi-Role Redirection Logic
            $redirectUrl = route('student.dashboard');
            if ($user->hasRole('Admin')) {
                $redirectUrl = route('admin.dashboard');
            }

            return response()->json([
                'status' => 'success',
                'redirect_url' => $redirectUrl,
            ]);

        } catch (\Exception $e) {
            Log::error('Registration Flow Error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json(['status' => 'error', 'message' => 'An unexpected error occurred. Please contact support.']);
        }
    }

    /**
     * Helper: Mask Strings (Mobile/Email/Name)
     */
    private function maskString($string, $type = 'text')
    {
        if (empty($string)) {
            return '';
        }

        if ($type === 'email') {
            $parts = explode('@', $string);
            if (count($parts) !== 2) {
                return $string;
            }

            $name = $parts[0];
            $domain = $parts[1];

            $maskedName = strlen($name) > 2
                ? $name[0].str_repeat('*', strlen($name) - 2).$name[strlen($name) - 1]
                : $name.'***';

            return $maskedName.'@'.$domain;
        }

        // Default: Mobile or Name
        return strlen($string) > 2
            ? $string[0].str_repeat('*', strlen($string) - 2).$string[strlen($string) - 1]
            : $string;
    }
}
