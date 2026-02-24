<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\User;
use App\Models\AffiliateCommission;
use App\Models\Tax;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class RegistrationService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    /**
     * Verify Razorpay Payment and Complete Registration
     */
    public function verifyAndCompleteRegistration(array $data)
    {
        return DB::transaction(function () use ($data) {
            // 1. Verify Signature
            $attributes = [
                'razorpay_order_id'   => $data['razorpay_order_id'],
                'razorpay_payment_id' => $data['razorpay_payment_id'],
                'razorpay_signature'  => $data['razorpay_signature']
            ];
            $this->api->utility->verifyPaymentSignature($attributes);

            // 2. Retrieve Data with Lock
            $lead = Lead::where('id', $data['lead_id'])->lockForUpdate()->firstOrFail();
            $bundle = Bundle::findOrFail($lead->product_preference['bundle_id']);

            // 3. Resolve Referrer
            $referrer = $this->resolveReferrer($lead);

            // 4. Create User
            $user = $this->createUser($lead, $referrer);

            // 5. Finalize Pricing and Coupons
            $pricing = $this->calculatePricing($bundle, $data['coupon_code'] ?? null, (bool)$referrer);
            $coupon = $this->handleCouponUsage($data['coupon_code'] ?? null);

            // 6. Record Payment
            $payment = $this->recordPayment($user, $bundle, $data, $pricing, $coupon);

            // 7. Process Affiliate Commission
            $this->processCommission($referrer, $user, $bundle);

            // 8. Cleanup Lead
            $lead->delete();

            return $user;
        });
    }

    /**
     * Centralized Pricing Logic
     */
    public function calculatePricing(Bundle $bundle, ?string $couponCode = null, bool $hasReferral = false)
    {
        $basePrice = $hasReferral ? $bundle->affiliate_price : $bundle->website_price;
        $discountAmount = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->active()->first();

            if (!$coupon) {
                throw new Exception('Invalid Coupon Code');
            }
            if ($coupon->isExpired()) {
                throw new Exception('Coupon Expired');
            }
            if ($coupon->isLimitReached()) {
                throw new Exception('Coupon Limit Reached');
            }
            if (!$coupon->isValidForItems([], [$bundle->id])) {
                throw new Exception('Coupon not valid for this bundle');
            }

            $discountAmount = $coupon->calculateDiscount($basePrice);
        }

        $baseAmountDue = max(0, $basePrice - $discountAmount);

        $taxes = Tax::where('is_active', true)->get();
        $totalTaxAmount = 0;
        $totalExclusiveTaxAmount = 0;
        $inclusiveTaxRateAmount = 0;

        // 1. Calculate Exclusive Taxes first
        foreach ($taxes as $tax) {
            if ($tax->tax_type === 'exclusive') {
                $currentTax = 0;
                if ($tax->type == 'percentage') {
                    $currentTax = ($baseAmountDue * $tax->value / 100);
                } else {
                    $currentTax = $tax->value;
                }
                $totalTaxAmount += $currentTax;
                $totalExclusiveTaxAmount += $currentTax;
                // Dynamically attach the calculated value for frontend use
                $tax->calculated_amount = $currentTax;
            }
        }

        // 2. Calculate Inclusive Taxes
        foreach ($taxes as $tax) {
            if ($tax->tax_type === 'inclusive') {
                $currentTax = 0;
                if ($tax->type == 'percentage') {
                    // For inclusive tax, the baseAmountDue *already contains* this tax
                    // Formula: Tax = Total - (Total / (1 + Rate))
                    $actualBaseWithoutThisTax = $baseAmountDue / (1 + ($tax->value / 100));
                    $currentTax = $baseAmountDue - $actualBaseWithoutThisTax;
                } else {
                    // Fixed inclusive tax (rare, but supported)
                    $currentTax = $tax->value;
                }
                $totalTaxAmount += $currentTax;
                $inclusiveTaxRateAmount += $currentTax;
                // Dynamically attach the calculated value for frontend use
                $tax->calculated_amount = $currentTax;
            }
        }

        // The subtotal (pre-tax amount) is the base amount due minus the inclusive taxes embedded within it
        $pureSubtotal = $baseAmountDue - $inclusiveTaxRateAmount;

        return [
            'basePrice'      => $basePrice,
            'websitePrice'   => $bundle->website_price,
            'affiliatePrice' => $bundle->affiliate_price,
            'discount'       => $discountAmount,
            'taxableAmount'  => $pureSubtotal,
            'taxAmount'      => $totalTaxAmount,
            'taxes'          => $taxes,
            // Total amount is the base amount due plus any *exclusive* taxes added on top
            'totalAmount'    => $baseAmountDue + $totalExclusiveTaxAmount
        ];
    }

    protected function resolveReferrer(Lead $lead)
    {
        $referralCode = $lead->referral_code ?: (session('referral_code') ?: Cookie::get('referral_code'));
        if ($referralCode) {
            return User::where('referral_code', $referralCode)->first();
        }
        return null;
    }

    protected function createUser(Lead $lead, ?User $referrer)
    {
        $user = User::create([
            'name'          => $lead->name,
            'email'         => $lead->email,
            'mobile'        => $lead->mobile,
            'password'      => $lead->password,
            'gender'        => $lead->gender,
            'dob'           => $lead->dob,
            'state_id'      => null,
            'referral_code' => Str::upper(Str::random(8)),
            'referred_by'   => $referrer ? $referrer->id : null,
            'is_active'     => true,
        ]);

        $user->assignRole('Student');
        return $user;
    }

    protected function handleCouponUsage(?string $couponCode)
    {
        if (!$couponCode) return null;

        $coupon = Coupon::where('code', $couponCode)->active()->first();
        if ($coupon) {
            $coupon->increment('used_count');
            return $coupon;
        }
        return null;
    }

    protected function recordPayment(User $user, Bundle $bundle, array $data, array $pricing, ?Coupon $coupon)
    {
        return Payment::create([
            'user_id'             => $user->id,
            'bundle_id'           => $bundle->id,
            'razorpay_order_id'   => $data['razorpay_order_id'],
            'razorpay_payment_id' => $data['razorpay_payment_id'],
            'subtotal'            => $pricing['taxableAmount'],
            'discount_amount'     => $pricing['discount'],
            'tax_amount'          => $pricing['taxAmount'],
            'tax_details'         => collect($pricing['taxes'])->map(function($tax) {
                return [
                    'name' => $tax->name,
                    'value' => $tax->value,
                    'type' => $tax->type,
                    'tax_type' => $tax->tax_type,
                    'calculated_amount' => $tax->calculated_amount ?? 0,
                ];
            })->toArray(),
            'total_amount'        => $pricing['totalAmount'],
            'amount'              => $pricing['totalAmount'],
            'coupon_id'           => $coupon ? $coupon->id : null,
            'status'              => 'success',
        ]);
    }

    protected function processCommission(?User $referrer, User $user, Bundle $bundle)
    {
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
    }
}
