<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CoursePurchasedMail;
use App\Mail\PlanUpgradedMail;
use App\Mail\AdminNotificationMail;
use App\Services\EmailService;

class RazorpayController extends Controller
{
    private $commissionService;

    public function __construct(\App\Services\CommissionCalculatorService $commissionService)
    {
        $this->commissionService = $commissionService;
    }

    private function calculatePricing($basePrice)
    {
        $baseAmountDue = max(0, $basePrice);
        $taxes = \App\Models\Tax::where('is_active', true)->get();
        $totalTaxAmount = 0;
        $totalExclusiveTaxAmount = 0;
        $inclusiveTaxRateAmount = 0;

        foreach ($taxes as $tax) {
            if ($tax->tax_type === 'exclusive') {
                $currentTax = ($tax->type == 'percentage') ? ($baseAmountDue * $tax->value / 100) : $tax->value;
                $totalTaxAmount += $currentTax;
                $totalExclusiveTaxAmount += $currentTax;
                $tax->calculated_amount = $currentTax;
            }
        }

        foreach ($taxes as $tax) {
            if ($tax->tax_type === 'inclusive') {
                $currentTax = ($tax->type == 'percentage')
                    ? ($baseAmountDue - ($baseAmountDue / (1 + ($tax->value / 100))))
                    : $tax->value;
                $totalTaxAmount += $currentTax;
                $inclusiveTaxRateAmount += $currentTax;
                $tax->calculated_amount = $currentTax;
            }
        }

        $pureSubtotal = $baseAmountDue - $inclusiveTaxRateAmount;

        return [
            'basePrice'      => $basePrice,
            'taxableAmount'  => $pureSubtotal,
            'taxAmount'      => $totalTaxAmount,
            'taxes'          => $taxes,
            'totalAmount'    => $baseAmountDue + $totalExclusiveTaxAmount
        ];
    }

    public function checkout($type, $id)
    {
        if (!in_array($type, ['course', 'bundle'])) {
            abort(404);
        }

        $user = Auth::user();
        $product = null;
        $amount = 0;

        if ($type === 'course') {
            $product = Course::findOrFail($id);
            $amount = ($user && $user->referrer) ? $product->affiliate_price : $product->final_price;
        } else {
            $product = \App\Models\Bundle::findOrFail($id);
            $amount = $product->getEffectivePriceForUser($user);
        }

        $pricing = $this->calculatePricing($amount);

        // If amount is 0, we can directly handle it
        if ($amount == 0) {
            // Can redirect or just render the checkout where price is 0
        }

        return view('student.checkout.index', array_merge(
            compact('product', 'type', 'id', 'user'),
            $pricing
        ));
    }

    public function createOrder(Request $request, $type, $id)
    {
        try {
            if (!in_array($type, ['course', 'bundle'])) {
                return response()->json(['status' => 'error', 'message' => 'Invalid product type.'], 400);
            }

            $user = Auth::user();
            $amount = 0;
            $productName = '';

            if ($type === 'course') {
                $course = Course::findOrFail($id);
                $amount = ($user && $user->referrer) ? $course->affiliate_price : $course->final_price;
                $productName = $course->title;
            } else {
                $bundle = \App\Models\Bundle::findOrFail($id);
                $amount = $bundle->getEffectivePriceForUser($user);
                $productName = $bundle->title;
            }

            // Razorpay expects amount in paise (1 INR = 100 Paise)
            $orderData = [
                'receipt'         => 'rcpt_' . time(),
                'amount'          => intval(round($amount * 100)),
                'currency'        => 'INR',
                'payment_capture' => 1
            ];

            // If amount is 0 (maybe fully discounted/upgraded), handle 0 payment automatically
            if ($orderData['amount'] == 0) {
                // Directly create a successful payment
                Payment::create([
                    'user_id' => $user->id,
                    'course_id' => $type === 'course' ? $id : null,
                    'bundle_id' => $type === 'bundle' ? $id : null,
                    'razorpay_order_id' => 'free_upg_' . time(),
                    'razorpay_payment_id' => 'free_upg_' . $user->id . '_' . time(),
                    'amount' => 0,
                    'status' => 'success',
                ]);
                // Success - Flash message for dashboard
                session()->flash('success', 'Investment Successful! Your ' . ($type == 'bundle' ? 'Bundle' : 'Course') . ' has been activated.');

                // Fire email for free/zero-amount upgrade
                try {
                    $productName = isset($bundle) ? $bundle->title : (isset($course) ? $course->title : 'Your Product');
                    $mailClass = ($type === 'bundle') ? PlanUpgradedMail::class : CoursePurchasedMail::class;
                    Mail::to($user->email)->queue(new $mailClass($user->name, $productName, '0', 'FREE-' . time()));
                    $adminEmail = EmailService::adminEmail();
                    if ($adminEmail) {
                        Mail::to($adminEmail)->queue(new AdminNotificationMail(
                            'New ' . ($type === 'bundle' ? 'Plan Upgrade' : 'Course Purchase'),
                            "{$user->name} ({$user->email}) just activated: {$productName} (Free upgrade)"
                        ));
                    }
                } catch (\Throwable $ignored) {}

                return response()->json(['status' => 'success']);
            }

            $gateway = \App\Services\Gateways\PaymentGatewayFactory::make();
            $gatewayName = $gateway->getGatewayName();

            $orderResult = $gateway->createOrder([
                'amount'     => $amount,
                'receipt'    => 'rcpt_' . time(),
                'currency'   => 'INR',
                'customer'   => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->mobile,
                ],
                'notes'      => [
                    'user_id' => $user->id,
                    'type'    => $type,
                    'item_id' => $id,
                ],
                'return_url' => url('/'),
            ]);

            // Store pending payment in database
            Payment::create([
                'user_id' => $user->id,
                'course_id' => $type === 'course' ? $id : null,
                'bundle_id' => $type === 'bundle' ? $id : null,
                'razorpay_order_id' => $gatewayName === 'razorpay' ? $orderResult['order_id'] : null,
                'gateway_order_id' => $orderResult['order_id'],
                'payment_gateway' => $gatewayName,
                'amount' => $amount,
                'subtotal' => $amount,
                'total_amount' => $amount,
                'status' => 'pending',
            ]);

            $response = [
                'gateway' => $gatewayName,
                'order_id' => $orderResult['order_id'],
                'amount' => $orderResult['amount'],
                'key' => $orderResult['key'],
                'product_name' => $productName,
            ];

            if ($gatewayName === 'cashfree') {
                $response['session_id'] = $orderResult['session_id'] ?? null;
                $response['environment'] = $orderResult['environment'] ?? 'sandbox';
            }

            return response()->json($response);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error creating payment order for {$type} {$id} (User " . Auth::id() . "): " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to initialize payment gateway. Please try again later.'], 500);
        }
    }

    /**
     * Handle payment verification after gateway callback.
     */
    public function verifyPayment(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $gatewayName = $request->input('gateway', 'razorpay');
            $gateway = \App\Services\Gateways\PaymentGatewayFactory::make($gatewayName);
            $orderId = null;
            $paymentId = null;

            if ($gatewayName === 'razorpay') {
                $orderId = $request->razorpay_order_id;
                $paymentId = $request->razorpay_payment_id;
                $gateway->verifyPayment([
                    'razorpay_order_id' => $orderId,
                    'razorpay_payment_id' => $paymentId,
                    'razorpay_signature' => $request->razorpay_signature
                ]);
            } else {
                $orderId = $request->cashfree_order_id;
                $verifyResult = $gateway->verifyPayment(['order_id' => $orderId]);
                if (!$verifyResult['verified']) {
                    throw new \Exception('Payment verification failed.');
                }
                $paymentId = $verifyResult['payment_id'];
            }

            \Illuminate\Support\Facades\DB::beginTransaction();

            $payment = Payment::with(['course', 'bundle'])
                ->where('gateway_order_id', $orderId)
                ->orWhere('razorpay_order_id', $orderId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payment->status !== 'success') {
                $this->processSuccessfulPayment($payment, (string) $paymentId);
            }

            \Illuminate\Support\Facades\DB::commit();

            // Send notification after commit
            $this->sendPurchaseNotifications($payment, (string) $paymentId);

            session()->flash('success', 'Investment Successful! Your ' . ($payment->bundle_id ? 'Bundle' : 'Course') . ' has been activated.');
            return response()->json(['status' => 'success', 'message' => 'Payment Verified']);

        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            \Illuminate\Support\Facades\Log::warning("Invalid Razorpay signature for order {$request->razorpay_order_id}");
            return response()->json(['status' => 'error', 'message' => 'Payment signature verification failed.'], 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Payment record not found.'], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error("Error verifying payment for order {$orderId}: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Payment verification failed due to a server error.'], 500);
        }
    }

    /**
     * Process successful payment: updates status, calculates commissions, and upgrades bundles.
     */
    private function processSuccessfulPayment(Payment $payment, string $paymentId): void
    {
        $user = Auth::user();
        $product = $payment->bundle ?? $payment->course;
        if (!$product || !$user) return;

        // Detect Upgrade
        $isUpgrade = false;
        $previousBundle = null;

        if ($product instanceof \App\Models\Bundle) {
            $previousBundle = $user->highestPurchasedBundle();
            if ($previousBundle && $previousBundle->id !== $product->id
                && $previousBundle->preference_index < $product->preference_index) {
                $isUpgrade = true;
            }
        }

        $payment->update([
            'status' => 'success',
            'razorpay_payment_id' => $paymentId,
            'gateway_payment_id' => $paymentId
        ]);

        // Process Commission
        if ($user->referrer) {
            $sponsor = $user->referrer;
            $commissionAmount = 0;

            if ($product instanceof \App\Models\Bundle) {
                if ($isUpgrade && $previousBundle) {
                    $newComm = $this->commissionService->calculateCommission($sponsor, $product, $product->affiliate_price);
                    $oldComm = $this->commissionService->calculateCommission($sponsor, $previousBundle, $previousBundle->affiliate_price);
                    $commissionAmount = max(0, $newComm - $oldComm);
                } else {
                    $commissionAmount = $this->commissionService->calculateCommission($sponsor, $product, $payment->amount);
                }
            } elseif ($product instanceof Course) {
                $commissionAmount = $product->commission_value ?? 0;
            }

            if ($commissionAmount > 0) {
                app(\App\Services\WalletService::class)->processCommission([
                    'affiliate_id' => $sponsor->id,
                    'referred_user_id' => $user->id,
                    'amount' => $commissionAmount,
                    'reference_id' => $product->id,
                    'reference_type' => get_class($product),
                    'notes' => ($isUpgrade ? 'Upgrade Commission' : 'Commission') . ' for ' . class_basename($product) . ': ' . $product->title,
                ]);
            }
        }
    }

    /**
     * Send email notifications for the purchase.
     */
    private function sendPurchaseNotifications(Payment $payment, string $paymentId): void
    {
        try {
            $product = $payment->bundle ?? $payment->course;
            if (!$product) return;

            $user = Auth::user();
            if (!$user) return;

            $productName = $product->title;
            $amountFormatted = number_format($payment->amount, 2);

            if ($payment->bundle_id) {
                Mail::to($user->email)->queue(new PlanUpgradedMail($user->name, $productName, $amountFormatted, $paymentId));
            } else {
                Mail::to($user->email)->queue(new CoursePurchasedMail($user->name, $productName, $amountFormatted, $paymentId));
            }

            $adminEmail = EmailService::adminEmail();
            if ($adminEmail) {
                Mail::to($adminEmail)->queue(new AdminNotificationMail(
                    'New ' . ($payment->bundle_id ? 'Plan Purchase' : 'Course Purchase'),
                    "{$user->name} ({$user->email}) purchased: {$productName} for ₹{$amountFormatted} (TXN: {$paymentId})"
                ));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Notification Error: " . $e->getMessage());
        }
    }
}
