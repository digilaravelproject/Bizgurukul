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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Razorpay\Api\Api;

class RegistrationFlowController extends Controller
{
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
            ]);

            $lead = Lead::updateOrCreate(
                ['email' => $request->email],
                [
                    'name'       => $request->name,
                    'mobile'     => $request->mobile,
                    'password'   => bcrypt($request->password), // Hashed here
                    'gender'     => $request->gender,
                    'dob'        => $request->dob,
                    'state'      => $request->state,
                    'ip_address' => $request->ip(),
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

            $referralCode = $lead->referral_code ?? Cookie::get('referral_code');
            $sponsor = $referralCode ? User::where('referral_code', $referralCode)->first() : null;

            $bundles = Bundle::where('is_active', true)
                ->orderBy('preference_index', 'asc')
                ->get();

            $maskedSponsor = $sponsor ? (object) [
                'name'   => $this->maskString($sponsor->name),
                'mobile' => $this->maskString($sponsor->mobile, 'mobile')
            ] : null;

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
     * Phase 2: Store Product & Sponsor
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

            // Centralized Pricing Logic
            $pricing = $this->calculatePricing($bundle);

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
            $pricing = $this->calculatePricing($bundle, $request->code);

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
            return response()->json(['status' => 'error', 'message' => 'Error applying coupon']);
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

            // Recalculate Price Securely
            $pricing = $this->calculatePricing($bundle, $request->coupon_code);

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
        DB::beginTransaction();
        try {
            $request->validate([
                'razorpay_order_id'   => 'required',
                'razorpay_payment_id' => 'required',
                'razorpay_signature'  => 'required',
                'lead_id'             => 'required|exists:leads,id'
            ]);

            // 1. Verify Signature
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            $attributes = [
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature
            ];
            $api->utility->verifyPaymentSignature($attributes);

            // 2. Retrieve Data
            $lead = Lead::findOrFail($request->lead_id);
            $bundle = Bundle::findOrFail($lead->product_preference['bundle_id']);

            // 3. Create User (Use stored password hash)
            $user = User::create([
                'name'          => $lead->name,
                'email'         => $lead->email,
                'mobile'        => $lead->mobile,
                'password'      => $lead->password, // FIX: Use the hash from Lead table
                'gender'        => $lead->gender,
                'dob'           => $lead->dob,
                'state_id'      => null,
                'referral_code' => Str::upper(Str::random(8)),
                'referred_by'   => null,
                'is_active'     => true,
            ]);

            $user->assignRole('Student');

            // 4. Handle Referrer Link
            $referrer = null;
            if ($lead->referral_code) {
                $referrer = User::where('referral_code', $lead->referral_code)->first();
                if ($referrer) {
                    $user->referred_by = $referrer->id;
                    $user->save();
                }
            }

            // 5. Record Payment
            Payment::create([
                'user_id'             => $user->id,
                'bundle_id'           => $bundle->id,
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'amount'              => $bundle->affiliate_price,
                'status'              => 'success',
            ]);

            // 6. Calculate Commission
            if ($referrer) {
                $commissionService = app(\App\Services\CommissionCalculatorService::class);
                $commissionAmount = $commissionService->calculateCommission($referrer, $bundle);

                if ($commissionAmount > 0) {
                    AffiliateCommission::create([
                        'affiliate_id'     => $referrer->id,
                        'referred_user_id' => $user->id,
                        'amount'           => $commissionAmount,
                        'status'           => 'pending',
                        'reference_id'     => $bundle->id,
                        'reference_type'   => get_class($bundle),
                        'notes'            => 'Commission for Bundle: ' . $bundle->title,
                    ]);
                }
            }

            // 7. Cleanup Lead
            $lead->delete();

            DB::commit();

            Auth::login($user);

            return response()->json([
                'status'       => 'success',
                'redirect_url' => route('student.dashboard')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Verification Failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Payment verification failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Helper: Centralized Pricing Logic
     */
    private function calculatePricing(Bundle $bundle, ?string $couponCode = null)
    {
        $basePrice = $bundle->affiliate_price;
        $discountAmount = 0;

        // Apply Coupon
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->active()->first();

            if (!$coupon) {
                return ['error' => 'Invalid Coupon Code'];
            }
            if ($coupon->isExpired()) {
                return ['error' => 'Coupon Expired'];
            }
            if ($coupon->isLimitReached()) {
                return ['error' => 'Coupon Limit Reached'];
            }
            if (!$coupon->isValidForItems([], [$bundle->id])) {
                return ['error' => 'Coupon not valid for this bundle'];
            }

            $discountAmount = $coupon->calculateDiscount($basePrice);
        }

        $taxableAmount = max(0, $basePrice - $discountAmount);

        // Calculate Tax
        $taxes = Tax::where('is_active', true)->get();
        $taxAmount = 0;

        foreach ($taxes as $tax) {
            if ($tax->type == 'percentage') {
                $taxAmount += ($taxableAmount * $tax->value / 100);
            } else {
                $taxAmount += $tax->value;
            }
        }

        return [
            'basePrice'     => $basePrice,
            'websitePrice'  => $bundle->website_price,
            'discount'      => $discountAmount,
            'taxableAmount' => $taxableAmount,
            'taxAmount'     => $taxAmount,
            'taxes'         => $taxes,
            'totalAmount'   => $taxableAmount + $taxAmount
        ];
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
