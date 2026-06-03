<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Coupon;
use App\Services\CouponService;

class IssueMissingCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupons:issue-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find all successful CouponPackage payments that have not been issued a coupon and issue them.';

    /**
     * Execute the console command.
     */
    public function handle(CouponService $couponService)
    {
        $this->info('Searching for successful CouponPackage payments with no coupon...');

        $payments = Payment::where('paymentable_type', \App\Models\CouponPackage::class)
            ->where('status', 'success')
            ->whereNull('coupon_id')
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No missing coupons found.');
            return 0;
        }

        $this->info("Found {$payments->count()} payment(s) missing coupons. Starting issuance...");

        foreach ($payments as $payment) {
            try {
                $coupon = $couponService->issueCouponForPayment($payment);
                $this->info("Issued coupon {$coupon->code} for payment ID {$payment->id} (User: {$payment->user->name}).");
            } catch (\Exception $e) {
                $this->error("Failed to issue coupon for payment ID {$payment->id}: {$e->getMessage()}");
            }
        }

        $this->info('Issuance completed.');
        return 0;
    }
}
