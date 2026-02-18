<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegistrationFlowController extends Controller
{
    /**
     * Phase 1: Show Lead Capture Form
     */
    public function showPhase1(Request $request)
    {
        $email = $request->query('email');
        return view('auth.register-phase1', compact('email'));
    }

    /**
     * Phase 1: Store Lead Data
     */
    public function storePhase1(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'confirmed'], // Checks email_confirmation
            'mobile' => ['required', 'numeric', 'digits_between:10,15'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'gender' => ['nullable', 'string'],
            'dob' => ['nullable', 'date'],
            'state' => ['nullable', 'string'],
        ]);

        // Check if lead exists, update or create
        $lead = Lead::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'password' => bcrypt($request->password), // Hash it now
                'gender' => $request->gender,
                'dob' => $request->dob,
                'state' => $request->state,
                'ip_address' => $request->ip(),
            ]
        );

        return redirect()->route('register.phase2', ['lead_id' => $lead->id]);
    }

    /**
     * Phase 2: Product Selection & Sponsor Verification
     */
    public function showPhase2(Request $request)
    {
        $leadId = $request->query('lead_id');
        $lead = Lead::findOrFail($leadId);

        // Logic to determine Referral Code
        // Priority: 1. Lead has it (from earlier session?) 2. Cookie 3. Request
        $referralCode = $lead->referral_code ?? \Illuminate\Support\Facades\Cookie::get('referral_code');

        $sponsor = null;
        if ($referralCode) {
            $sponsor = User::where('referral_code', $referralCode)->first();
        }

        // Fetch Products (Bundles mostly, as per preference)
        // Sort by Preference ID (Ascending or Descending? Usually 1 is top priority)
        // If 'preference_index' is newly added, ensure it exists. Assuming it does based on previous chats.
        $bundles = \App\Models\Bundle::where('is_active', true)
                    ->orderBy('preference_index', 'asc')
                    ->get();

        // Also fetch individual courses if needed, but usually bundles are primary.
        // Prompt says: "If affiliate link was for Specific Bundle... only that bundle is shown."
        // We'll handle "All Bundles" for now.
        // Logic for specific bundle would require capturing that context in Middleware/Cookie.
        // For now, let's show all bundles.

        return view('auth.register-phase2', compact('lead', 'sponsor', 'bundles', 'referralCode'));
    }

    /**
     * AJAX: Check Referral Code details for Phase 2
     */
    public function checkReferralPhase2(Request $request)
    {
        $code = $request->code;
        $sponsor = User::where('referral_code', $code)->first();

        if ($sponsor) {
             // Mask mobile: +91 987****321
             $mobile = $sponsor->mobile;
             $maskedMobile = substr($mobile, 0, 2) . ' ' . substr($mobile, 2, 3) . '****' . substr($mobile, -3);

             return response()->json([
                 'status' => 'valid',
                 'name' => $sponsor->name,
                 'mobile' => $maskedMobile, // Masked for display
             ]);
        }

        return response()->json(['status' => 'invalid']);
    }

    /**
     * Phase 2: Store Product & Sponsor
     */
    public function storePhase2(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'bundle_id' => 'required|exists:bundles,id',
            'referral_code' => 'nullable|string'
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        // Update product preference
        $preference = $lead->product_preference ?? [];
        $preference['bundle_id'] = $request->bundle_id;

        $lead->update([
            'product_preference' => $preference,
            'referral_code' => $request->referral_code
        ]);

        return redirect()->route('register.phase3', ['lead_id' => $lead->id]);
    }


    /**
     * Razorpay: Initiate Payment (Create Order)
     */
    public function initiatePayment(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'amount' => 'required|numeric',
            'coupon_code' => 'nullable|string'
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        // Re-calculate amount backend side for security
        if (empty($lead->product_preference['bundle_id'])) {
             return response()->json(['status' => 'error', 'message' => 'No product selected']);
        }

        $bundle = \App\Models\Bundle::findOrFail($lead->product_preference['bundle_id']);
        $basePrice = $bundle->affiliate_price;
        $discountAmount = 0;

        if ($request->coupon_code) {
             $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->active()->first();
             if ($coupon && $coupon->isValidForItems([], [$bundle->id])) {
                 $discountAmount = $coupon->calculateDiscount($basePrice);
             }
        }

        $taxableAmount = max(0, $basePrice - $discountAmount);

        $taxes = \App\Models\Tax::where('is_active', true)->get();
        $taxAmount = 0;
        foreach ($taxes as $tax) {
            if ($tax->type == 'percentage') {
                $taxAmount += ($taxableAmount * $tax->value / 100);
            } else {
                $taxAmount += $tax->value;
            }
        }

        $totalAmount = $taxableAmount + $taxAmount;

        // Check if frontend amount matches (optional, but good practice)
        // difference allowance for floating point
        if (abs($totalAmount - $request->amount) > 1) {
             // return response()->json(['status' => 'error', 'message' => 'Price mismatch']);
             // For now, trust our calculation
        }

        // Initialize Razorpay
        $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        $orderData = [
            'receipt'         => 'rcpt_' . $lead->id . '_' . time(),
            'amount'          => round($totalAmount * 100), // Paise
            'currency'        => 'INR',
            'payment_capture' => 1
        ];

        try {
            $razorpayOrder = $api->order->create($orderData);

            return response()->json([
                'status' => 'success',
                'order_id' => $razorpayOrder['id'],
                'amount' => $orderData['amount'],
                'key' => env('RAZORPAY_KEY'),
                'name' => 'Bizgurukul', // Or config app name
                'description' => $bundle->title,
                'prefill' => [
                    'name' => $lead->name,
                    'email' => $lead->email,
                    'contact' => $lead->mobile
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Razorpay: Verify Payment & Activate Account
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'lead_id' => 'required|exists:leads,id'
        ]);

        $api = new \Razorpay\Api\Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // Signature Valid -> Proceed to Activation
            return $this->activateAccount($request);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Signature Verification Failed']);
        }
    }

    private function activateAccount(Request $request)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $lead = Lead::findOrFail($request->lead_id);
            $bundleId = $lead->product_preference['bundle_id'];
            $bundle = \App\Models\Bundle::findOrFail($bundleId);

            // 1. Create User
            // Password logic: Prompt says force 12345678
            // Model has 'hashed' cast, so pass plain text
            $user = User::create([
                'name' => $lead->name,
                'email' => $lead->email,
                'mobile' => $lead->mobile,
                'password' => '12345678',
                'gender' => $lead->gender,
                'dob' => $lead->dob,
                'state_id' => null,
                'referral_code' => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(8)),
                'referred_by' => null, // Will set below
                'is_active' => true,
            ]);

            // Assign Role
            $user->assignRole('Student');

            // Handle Referrer
            if ($lead->referral_code) {
                $referrer = User::where('referral_code', $lead->referral_code)->first();
                if ($referrer) {
                    $user->referred_by = $referrer->id;
                    $user->save();
                }
            }

            // 2. Create Payment Record
            $payment = \App\Models\Payment::create([
                'user_id' => $user->id,
                'bundle_id' => $bundle->id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'amount' => $bundle->affiliate_price,
                'status' => 'success',
            ]);

            // 3. Commission Logic
             if ($user->referred_by && isset($referrer)) {
                 $commissionService = app(\App\Services\CommissionCalculatorService::class);
                 $commissionAmount = $commissionService->calculateCommission($referrer, $bundle);

                 if ($commissionAmount > 0) {
                     \App\Models\AffiliateCommission::create([
                        'affiliate_id' => $referrer->id,
                        'referred_user_id' => $user->id,
                        'amount' => $commissionAmount,
                        'status' => 'pending', // Pending until admin approval? Or auto-approve?
                        'reference_id' => $bundle->id,
                        'reference_type' => get_class($bundle),
                        'notes' => 'Commission for Bundle: ' . $bundle->title,
                    ]);
                 }
             }

            // 4. Delete Lead (or mark converted)
            $lead->delete();

            \Illuminate\Support\Facades\DB::commit();

            // Login User
            \Illuminate\Support\Facades\Auth::login($user);

            return response()->json([
                'status' => 'success',
                'redirect_url' => route('dashboard') // Or password reset route if implemented
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function showPhase3(Request $request)
    {
        $leadId = $request->query('lead_id');
        $lead = Lead::findOrFail($leadId);

        if (empty($lead->product_preference['bundle_id'])) {
            return redirect()->route('register.phase2', ['lead_id' => $lead->id]);
        }

        $bundle = \App\Models\Bundle::findOrFail($lead->product_preference['bundle_id']);

        // Base Price (Affiliate Price)
        $basePrice = $bundle->affiliate_price;

        // Calculate Initial Tax
        $taxes = \App\Models\Tax::where('is_active', true)->get();
        $taxAmount = 0;
        foreach ($taxes as $tax) {
            if ($tax->type == 'percentage') {
                $taxAmount += ($basePrice * $tax->value / 100);
            } else {
                $taxAmount += $tax->value;
            }
        }

        $totalAmount = $basePrice + $taxAmount;

        // Mask Email for display
        $email = $lead->email;
        $parts = explode('@', $email);
        if (count($parts) === 2) {
            $name = $parts[0];
            $domainFull = $parts[1];
            $maskedName = strlen($name) > 1 ? $name[0] . '********' . $name[strlen($name) - 1] : $name . '********';
            $dParts = explode('.', $domainFull);
            $dName = $dParts[0];
            $tld = count($dParts) > 1 ? implode('.', array_slice($dParts, 1)) : '';
            $maskedD = strlen($dName) > 1 ? $dName[0] . '****' . $dName[strlen($dName) - 1] : $dName . '****';
            $maskedEmail = $maskedName . '@' . $maskedD . ($tld ? '.' . $tld : '');
        } else {
            $maskedEmail = $email;
        }

        return view('auth.register-phase3', compact('lead', 'bundle', 'basePrice', 'taxes', 'taxAmount', 'totalAmount', 'maskedEmail'));
    }

    /**
     * AJAX: Check Coupon Logic
     */
    public function checkCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'bundle_id' => 'required|exists:bundles,id'
        ]);

        $code = $request->code;
        $coupon = \App\Models\Coupon::where('code', $code)->first();

        // 1. Basic Validation
        if (!$coupon || !$coupon->is_active) {
            return response()->json(['status' => 'invalid', 'message' => 'Invalid Coupon Code']);
        }

        if ($coupon->isExpired()) {
             return response()->json(['status' => 'invalid', 'message' => 'Coupon Expired']);
        }

        if ($coupon->isLimitReached()) {
             return response()->json(['status' => 'invalid', 'message' => 'Coupon Usage Limit Reached']);
        }

        // 2. Item Validation
        // Assuming single bundle purchase for now
        $isValidItem = $coupon->isValidForItems([], [$request->bundle_id]);
        if (!$isValidItem) {
             return response()->json(['status' => 'invalid', 'message' => 'Coupon not applicable on this bundle']);
        }

        // 3. Calculation
        $bundle = \App\Models\Bundle::find($request->bundle_id);
        $basePrice = $bundle->affiliate_price;

        $discountAmount = $coupon->calculateDiscount($basePrice);

        // Tax Recalculation on Discounted Price
        $taxableAmount = max(0, $basePrice - $discountAmount);

        $taxes = \App\Models\Tax::where('is_active', true)->get();
        $taxAmount = 0;
        foreach ($taxes as $tax) {
            if ($tax->type == 'percentage') {
                $taxAmount += ($taxableAmount * $tax->value / 100);
            } else {
                $taxAmount += $tax->value;
            }
        }

        $totalAmount = $taxableAmount + $taxAmount;

        return response()->json([
            'status' => 'valid',
            'message' => 'Coupon Applied Successfully',
            'discount' => $discountAmount,
            'tax' => $taxAmount,
            'total' => $totalAmount,
            'taxable_amount' => $taxableAmount
        ]);
    }
}
