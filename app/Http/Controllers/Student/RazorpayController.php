<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\Course;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class RazorpayController extends Controller
{
    private $api;
    private $commissionService;

    public function __construct(\App\Services\CommissionCalculatorService $commissionService)
    {
        $this->api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
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
            $amount = $product->final_price;
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
                $amount = $course->final_price;
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

                return response()->json(['status' => 'success']);
            }

            $razorpayOrder = $this->api->order->create($orderData);

            // Store pending payment in database
            Payment::create([
                'user_id' => $user->id,
                'course_id' => $type === 'course' ? $id : null,
                'bundle_id' => $type === 'bundle' ? $id : null,
                'razorpay_order_id' => $razorpayOrder['id'],
                'amount' => $amount,
                'subtotal' => $amount,
                'total_amount' => $amount,
                'status' => 'pending',
            ]);

            return response()->json([
                'order_id' => $razorpayOrder['id'],
                'amount' => $orderData['amount'],
                'key' => env('RAZORPAY_KEY'),
                'product_name' => $productName,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 'error', 'message' => 'Product not found.'], 404);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error creating Razorpay order for {$type} {$id} (User " . Auth::id() . "): " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to initialize payment gateway. Please try again later.'], 500);
        }
    }

    public function verifyPayment(Request $request)
    {
        try {
            $signature = $request->razorpay_signature;
            $paymentId = $request->razorpay_payment_id;
            $orderId = $request->razorpay_order_id;

            $attributes = [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature
            ];

            $this->api->utility->verifyPaymentSignature($attributes);

            // DB Transaction for ACID compliance
            \Illuminate\Support\Facades\DB::beginTransaction();

            // Update Database
            $payment = Payment::with(['course', 'bundle'])->where('razorpay_order_id', $orderId)->lockForUpdate()->firstOrFail();

            if ($payment->status !== 'success') {
                $payment->update([
                    'status' => 'success',
                    'razorpay_payment_id' => $paymentId
                ]);

                // Determine Product
                $product = $payment->bundle ?? $payment->course;

                // Commission Logic
                $user = Auth::user();
                // Check if referred (either by direct referrer or via cookie/affiliate link logic)
                // Assuming user->referred_by serves as the sponsor
                if ($user && $user->referrer) {
                    $sponsor = $user->referrer;

                    // Calculate Commission
                    $commissionAmount = 0;
                    if ($product instanceof \App\Models\Bundle) {
                         $commissionAmount = $this->commissionService->calculateCommission($sponsor, $product, $payment->amount);
                    } elseif ($product instanceof Course) {
                         $commissionAmount = $product->commission_value ?? 0;
                    }

                    if ($commissionAmount > 0) {
                        app(\App\Services\WalletService::class)->processCommission([
                            'affiliate_id' => $sponsor->id,
                            'referred_user_id' => $user->id,
                            'amount' => $commissionAmount,
                            // Status is handled by processCommission
                            'reference_id' => $product->id,
                            'reference_type' => get_class($product),
                            'notes' => 'Commission for ' . class_basename($product) . ': ' . $product->title,
                        ]);
                    }
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            // Success - Flash message for dashboard
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
            \Illuminate\Support\Facades\Log::error("Error verifying Razorpay payment for order {$request->razorpay_order_id}: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Payment verification failed due to a server error.'], 500);
        }
    }
}
