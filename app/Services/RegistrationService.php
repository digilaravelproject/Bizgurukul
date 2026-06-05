<?php

namespace App\Services;

use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\User;
use App\Models\Tax;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\WelcomeMail;
use App\Mail\AdminNotificationMail;
use App\Exceptions\InvalidCouponException;
use Exception;

class RegistrationService
{
    /** @var \Razorpay\Api\Api|null */
    protected $api;

    public function __construct()
    {
        // No longer hardcoding Razorpay here to support multi-gateway
    }

    /**
     * Verify Payment and Complete Registration
     */
    public function verifyAndCompleteRegistration(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $gatewayName = $data['gateway'] ?? \App\Services\Gateways\PaymentGatewayFactory::activeGateway();
                $gateway = \App\Services\Gateways\PaymentGatewayFactory::make($gatewayName);

                // 1. Verify Payment (Bypass redirect-signature-verification if it's already verified by Webhook)
                if (empty($data['is_webhook'])) {
                    $verifyResult = $gateway->verifyPayment($data);

                    if (!$verifyResult['verified']) {
                        throw new Exception('Payment verification failed: ' . ($verifyResult['error'] ?? 'Invalid details'));
                    }

                    // Update data with the actual gateway payment ID if found during verification
                    if (!empty($verifyResult['payment_id'])) {
                        $data['gateway_payment_id'] = $verifyResult['payment_id'];
                    }
                }

                // 2. Retrieve Data with Lock to prevent duplicate processing
                /** @var Lead|null $lead */
                $lead = Lead::where('id', '=', $data['lead_id'], 'and')->lockForUpdate()->first();

                if (!$lead) {
                    $orderId = $data['gateway_order_id'] ?? $data['cashfree_order_id'] ?? $data['razorpay_order_id'] ?? null;
                    if ($orderId) {
                        $payment = Payment::where('gateway_order_id', '=', $orderId, 'and')
                            ->orWhere('razorpay_order_id', '=', $orderId)
                            ->first();
                        if ($payment && $payment->user) {
                            throw new \App\Exceptions\LeadAlreadyProcessedException($payment->user);
                        }
                    }

                    $txnId = $data['gateway_payment_id'] ?? $data['gateway_order_id'] ?? 'N/A';
                    throw new Exception("Registration record (Lead) not found. Your payment might have been successful, but we couldn't complete the registration automatically. Please contact support with Transaction ID: {$txnId}");
                }

                $bundleId = $lead->product_preference['bundle_id'] ?? null;
                if (!$bundleId) {
                    throw new Exception("Product preference (bundle_id) is missing for Lead ID: {$lead->id}");
                }

                $bundle = Bundle::findOrFail($bundleId);

                // 3. Resolve Referrer
                $referrer = $this->resolveReferrer($lead);

                // 4. Create User
                $user = $this->createUser($lead, $referrer);

                // 5. Finalize Pricing and Coupons
                $pricing = $this->calculatePricing($bundle, $data['coupon_code'] ?? null, (bool) $referrer);
                $coupon = $this->handleCouponUsage($data['coupon_code'] ?? null);

                // 6. Record Payment
                $payment = $this->recordPayment($user, $bundle, $data, $pricing, $coupon);

                // 7. Process Affiliate Commission
                $this->processCommission($referrer, $user, $bundle);

                // 8. Cleanup Lead
                Lead::destroy($lead->id);

                // 9. Send Notifications
                try {
                    // To User
                    Mail::to($user->email)->send(new WelcomeMail($user));

                    // To Admin
                    $adminEmail = \App\Services\EmailService::adminEmail() ?: config('mail.from.address');
                    Mail::to($adminEmail)->send(new AdminNotificationMail(
                        'New Student Registration',
                        "A new student {$user->name} ({$user->email}) has successfully registered and purchased the bundle: {$bundle->title}."
                    ));
                } catch (Exception $e) {
                    // Log error but don't fail registration
                    Log::error("Registration Email Failed: " . $e->getMessage());
                }

                return $user;
            });
        } catch (\App\Exceptions\LeadAlreadyProcessedException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::error('Registration Verification Failed', [
                'error' => $e->getMessage(),
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Centralized Pricing Logic
     */
    public function calculatePricing(Bundle $bundle, ?string $couponCode = null, bool $hasReferral = false)
    {
        $basePrice = $hasReferral ? $bundle->affiliate_price : $bundle->website_price;
        $discountAmount = 0;

        if ($couponCode) {
            $coupon = Coupon::where('code', '=', $couponCode, 'and')->active()->first();

            if (!$coupon) {
                throw new InvalidCouponException('This coupon code is invalid or expired. Please remove it to proceed with the payment.');
            }
            if ($coupon->isExpired()) {
                throw new InvalidCouponException('This coupon code is invalid or expired. Please remove it to proceed with the payment.');
            }
            if ($coupon->isLimitReached()) {
                throw new InvalidCouponException('This coupon code limit has been reached. Please remove it to proceed with the payment.');
            }
            if (!$coupon->isValidForItems([], [$bundle->id])) {
                throw new InvalidCouponException('This coupon is not valid for the selected bundle. Please remove it to proceed with the payment.');
            }

            $discountAmount = $coupon->calculateDiscount($basePrice);
        }

        $baseAmountDue = max(0, $basePrice - $discountAmount);

        $taxes = Tax::where('is_active', '=', true, 'and')->get();
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
            'basePrice' => $basePrice,
            'websitePrice' => $bundle->website_price,
            'affiliatePrice' => $bundle->affiliate_price,
            'discount' => $discountAmount,
            'taxableAmount' => $pureSubtotal,
            'taxAmount' => $totalTaxAmount,
            'taxes' => $taxes,
            // Total amount is the base amount due plus any *exclusive* taxes added on top
            'totalAmount' => $baseAmountDue + $totalExclusiveTaxAmount
        ];
    }

    protected function resolveReferrer(Lead $lead)
    {
        $referralCode = $lead->referral_code ?: (session('referral_code') ?: Cookie::get('referral_code'));
        if ($referralCode) {
            return User::where('referral_code', '=', $referralCode, 'and')->first();
        }
        return null;
    }

    protected function createUser(Lead $lead, ?User $referrer)
    {
        // Sanitize name for database safety (remove 4-byte UTF-8 chars like emojis if DB charset is utf8)
        $sanitizedName = preg_replace('/[^\p{L}\p{N}\s\p{P}]/u', '', $lead->name);

        $user = User::create([
            'name' => trim($sanitizedName) ?: 'User',
            'email' => $lead->email,
            'mobile' => $lead->mobile,
            'password' => $lead->password,
            'gender' => $lead->gender,
            'dob' => $lead->dob,
            'state_id' => $lead->state_id,
            'city' => $lead->city,
            'referred_by' => $referrer ? $referrer->id : null,
            'is_active' => true,
        ]);

        $user->assignRole('Student');
        return $user;
    }

    protected function handleCouponUsage(?string $couponCode)
    {
        if (!$couponCode)
            return null;

        $coupon = Coupon::where('code', '=', $couponCode, 'and')->active()->first();
        if ($coupon) {
            $coupon->increment('used_count', 1);
            return $coupon;
        }
        return null;
    }

    protected function recordPayment(User $user, Bundle $bundle, array $data, array $pricing, ?Coupon $coupon)
    {
        $gatewayName = $data['gateway'] ?? 'razorpay';

        // Resolve the order ID from gateway-specific keys
        $orderId = $data['razorpay_order_id'] ?? $data['cashfree_order_id'] ?? $data['gateway_order_id'] ?? null;

        $paymentData = [
            'user_id' => $user->id,
            'lead_id' => null, // Clear lead association since user now exists
            'bundle_id' => $bundle->id,
            'subtotal' => $pricing['taxableAmount'],
            'discount_amount' => $pricing['discount'],
            'tax_amount' => $pricing['taxAmount'],
            'tax_details' => collect($pricing['taxes'])->map(function ($tax) {
                return [
                    'name' => $tax->name,
                    'value' => $tax->value,
                    'type' => $tax->type,
                    'tax_type' => $tax->tax_type,
                    'calculated_amount' => $tax->calculated_amount ?? 0,
                ];
            })->toArray(),
            'total_amount' => $pricing['totalAmount'],
            'amount' => $pricing['totalAmount'],
            'coupon_id' => $coupon ? $coupon->id : null,
            'status' => 'success',
            'created_at' => now(), // Sync payment time to current success/activation time
            'payment_gateway' => $gatewayName,
            'gateway_order_id' => $orderId,
            'gateway_payment_id' => $data['razorpay_payment_id'] ?? $data['cashfree_payment_id'] ?? $data['gateway_payment_id'] ?? null,
        ];

        // Maintain backward compatibility for legacy columns if they exist
        if ($gatewayName === 'razorpay') {
            $paymentData['razorpay_order_id'] = $paymentData['gateway_order_id'];
            $paymentData['razorpay_payment_id'] = $paymentData['gateway_payment_id'];
        }

        // Try to find and update existing pending payment (created by initiatePayment)
        // instead of creating a duplicate record
        /** @var Payment|null $existingPayment */
        $existingPayment = Payment::where('gateway_order_id', '=', $orderId, 'and')
            ->orWhere('razorpay_order_id', '=', $orderId)
            ->first();

        if ($existingPayment instanceof Payment) {
            $existingPayment->fill($paymentData)->save();
            return $existingPayment->fresh();
        }

        return Payment::create($paymentData);
    }

    protected function processCommission(?User $referrer, User $user, Bundle $bundle)
    {
        if ($referrer) {
            $commissionService = app(\App\Services\CommissionCalculatorService::class);
            $commissionAmount = $commissionService->calculateCommission($referrer, $bundle);

            if ($commissionAmount > 0) {
                app(\App\Services\WalletService::class)->processCommission([
                    'affiliate_id' => $referrer->id,
                    'referred_user_id' => $user->id,
                    'amount' => $commissionAmount,
                    'reference_id' => $bundle->id,
                    'reference_type' => get_class($bundle),
                    'notes' => 'Commission for Bundle: ' . $bundle->title,
                ]);
            }
        }
    }
}
