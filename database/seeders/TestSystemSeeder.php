<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bundle;
use App\Models\Payment;
use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * TestSystemSeeder — Realistic Test Data
 *
 * Creates students and affiliate sales that EXACTLY mirror how the real system
 * works:
 *   - Every commission is linked to a real Bundle purchase via reference_type +
 *     reference_id (as the WalletService does in production).
 *   - Commission amounts are a realistic % of the affiliate_price (never more
 *     than what the buyer actually paid).
 *   - Payments use the bundle's affiliate_price (referral sale) or website_price
 *     (direct sale), just like the real checkout flow.
 *   - TDS 2 % is deducted and stored in tds_amount / payable_amount.
 *   - Commissions spread across several time windows so every dashboard card
 *     (Today / 7 Days / 30 Days / All Time) shows a value.
 *
 * CLEANUP: Run `php artisan db:seed --class=TestSystemSeeder --rollback` or
 *   delete users whose email ends with @skpehle-test.dev.
 */
class TestSystemSeeder extends Seeder
{
    // ── Constants ──────────────────────────────────────────────────────────

    /** Identifier suffix for all test emails — easy to find & delete later */
    private const EMAIL_DOMAIN = '@skpehle-test.dev';

    /** Admin commission rate on the affiliate price (%) */
    private const COMMISSION_RATE = 50; // 50% of affiliate_price goes to affiliate

    /** TDS rate deducted from gross commission (%) */
    private const TDS_RATE = 2;

    /** Payment gateway fee (%) — for reference only in comments */
    private const GATEWAY_RATE = 2;

    // ── Main Runner ────────────────────────────────────────────────────────

    public function run(): void
    {
        DB::beginTransaction();

        try {
            // ── Step 1: Create affiliate accounts ──────────────────────────
            $affiliates = $this->createAffiliates();

            // ── Step 2: Create student buyers + payments + commissions ─────
            // Spread sales across time so all dashboard filters are non-zero
            $this->createSalesScenario($affiliates, daysAgo: 0,  label: 'Today');
            $this->createSalesScenario($affiliates, daysAgo: 3,  label: '3 Days Ago');
            $this->createSalesScenario($affiliates, daysAgo: 6,  label: '6 Days Ago');
            $this->createSalesScenario($affiliates, daysAgo: 15, label: '15 Days Ago');
            $this->createSalesScenario($affiliates, daysAgo: 25, label: '25 Days Ago');

            // ── Step 3: A few Direct (non-affiliate) sales ─────────────────
            // No commission generated — tests the "direct sale" path
            $this->createDirectSales(daysAgo: 0);
            $this->createDirectSales(daysAgo: 10);

            DB::commit();

            $this->printSummary($affiliates);

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('TestSystemSeeder FAILED: ' . $e->getMessage());
            throw $e;
        }
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    /**
     * Create 3 affiliate (Student) accounts with realistic Indian names.
     */
    private function createAffiliates(): array
    {
        $profiles = [
            ['name' => 'Rahul Verma',  'email' => 'rahul.verma'  . self::EMAIL_DOMAIN, 'mobile' => '9812345671'],
            ['name' => 'Priya Singh',  'email' => 'priya.singh'  . self::EMAIL_DOMAIN, 'mobile' => '9812345672'],
            ['name' => 'Amit Sharma',  'email' => 'amit.sharma'  . self::EMAIL_DOMAIN, 'mobile' => '9812345673'],
        ];

        $affiliates = [];
        foreach ($profiles as $profile) {
            $user = User::updateOrCreate(
                ['email' => $profile['email']],
                [
                    'name'       => $profile['name'],
                    'password'   => Hash::make('Test@1234'),
                    'mobile'     => $profile['mobile'],
                    'gender'     => 'male',
                    'dob'        => '1995-06-15',
                    'state_id'   => 10,
                    'is_active'  => true,
                    'kyc_status' => 'verified',
                ]
            );
            if (method_exists($user, 'assignRole') && !$user->hasRole('Student')) {
                $user->assignRole('Student');
            }
            $affiliates[] = $user;
        }

        return $affiliates;
    }

    /**
     * Create one affiliate sale scenario:
     *   - New student buyer is registered
     *   - Buyer pays the bundle's AFFILIATE price (referral purchase)
     *   - Commission is created for the affiliate with proper reference_type/id
     *
     * @param  User[]  $affiliates
     */
    private function createSalesScenario(array $affiliates, int $daysAgo, string $label): void
    {
        $bundles  = Bundle::all();
        $saleTime = Carbon::now()->subDays($daysAgo)->setTime(rand(8, 20), rand(0, 59));

        // Pick random affiliate & bundle
        $affiliate = $affiliates[array_rand($affiliates)];
        $bundle    = $bundles->random();

        // ── Buyer ────────────────────────────────────────────────────────
        $i = DB::table('users')->max('id') + 1;
        $buyer = User::updateOrCreate(
            ['email' => "buyer{$i}" . self::EMAIL_DOMAIN],
            [
                'name'        => "Test Buyer {$i}",
                'password'    => Hash::make('Test@1234'),
                'mobile'      => '98' . str_pad(rand(10000000, 99999999), 8, '0'),
                'gender'      => 'male',
                'dob'         => '2000-01-01',
                'state_id'    => rand(1, 28),
                'is_active'   => true,
                'kyc_status'  => 'verified',
                'referred_by' => $affiliate->id,  // linked to affiliate
            ]
        );
        if (method_exists($buyer, 'assignRole') && !$buyer->hasRole('Student')) {
            $buyer->assignRole('Student');
        }

        // ── Payment (affiliate price) ─────────────────────────────────────
        $paidAmount = $bundle->affiliate_price ?? $bundle->final_price;

        $payment = Payment::create([
            'user_id'              => $buyer->id,
            'bundle_id'            => $bundle->id,
            'paymentable_type'     => \App\Models\Bundle::class,
            'paymentable_id'       => $bundle->id,
            'amount'               => $paidAmount,
            'subtotal'             => $paidAmount,
            'discount_amount'      => 0,
            'tax_amount'           => round($paidAmount * 18 / 118, 2), // GST inclusive
            'total_amount'         => $paidAmount,
            'status'               => 'success',
            'razorpay_order_id'    => 'order_' . strtoupper(\Illuminate\Support\Str::random(14)),
            'razorpay_payment_id'  => 'pay_'   . strtoupper(\Illuminate\Support\Str::random(14)),
            'created_at'           => $saleTime,
            'updated_at'           => $saleTime,
        ]);

        // ── Commission (realistic % of affiliate price, never > paid amount) ─
        // GST-exclusive base: paidAmount × 100/118
        $amountExGst    = round($paidAmount * 100 / 118, 2);
        $commissionGross = round($amountExGst * self::COMMISSION_RATE / 100, 2);
        $tds             = round($commissionGross * self::TDS_RATE / 100, 2);
        $payable         = round($commissionGross - $tds, 2);

        AffiliateCommission::create([
            'affiliate_id'    => $affiliate->id,
            'referred_user_id'=> $buyer->id,
            'reference_type'  => \App\Models\Bundle::class,  // ← CRITICAL: must not be empty
            'reference_id'    => $bundle->id,
            'amount'          => $commissionGross,
            'tds_amount'      => $tds,
            'payable_amount'  => $payable,
            'status'          => 'paid',
            'notes'           => "Commission for Bundle: {$bundle->title} (Ref: {$label})",
            'available_at'    => $saleTime,
            'processed_at'    => $saleTime,
            'created_at'      => $saleTime,
            'updated_at'      => $saleTime,
        ]);
    }

    /**
     * Create a direct (non-affiliate) sale:
     * Buyer purchases on website_price, no referral, no commission.
     */
    private function createDirectSales(int $daysAgo): void
    {
        $bundle   = Bundle::inRandomOrder()->first();
        $saleTime = Carbon::now()->subDays($daysAgo)->setTime(rand(9, 18), rand(0, 59));
        $i        = DB::table('users')->max('id') + 1;

        $buyer = User::updateOrCreate(
            ['email' => "direct{$i}" . self::EMAIL_DOMAIN],
            [
                'name'       => "Direct Buyer {$i}",
                'password'   => Hash::make('Test@1234'),
                'mobile'     => '98' . str_pad(rand(10000000, 99999999), 8, '0'),
                'gender'     => 'female',
                'dob'        => '1998-03-20',
                'state_id'   => rand(1, 28),
                'is_active'  => true,
                'kyc_status' => 'verified',
            ]
        );
        if (method_exists($buyer, 'assignRole') && !$buyer->hasRole('Student')) {
            $buyer->assignRole('Student');
        }

        // Direct buyer pays website_price
        $paidAmount = $bundle->website_price ?? $bundle->final_price;

        Payment::create([
            'user_id'              => $buyer->id,
            'bundle_id'            => $bundle->id,
            'paymentable_type'     => \App\Models\Bundle::class,
            'paymentable_id'       => $bundle->id,
            'amount'               => $paidAmount,
            'subtotal'             => $paidAmount,
            'discount_amount'      => 0,
            'tax_amount'           => round($paidAmount * 18 / 118, 2),
            'total_amount'         => $paidAmount,
            'status'               => 'success',
            'razorpay_order_id'    => 'order_' . strtoupper(\Illuminate\Support\Str::random(14)),
            'razorpay_payment_id'  => 'pay_'   . strtoupper(\Illuminate\Support\Str::random(14)),
            'created_at'           => $saleTime,
            'updated_at'           => $saleTime,
        ]);
        // No commission — direct sale, no affiliate
    }

    /**
     * Print a readable summary after seeding.
     */
    private function printSummary(array $affiliates): void
    {
        $this->command->newLine();
        $this->command->info('✅ TestSystemSeeder completed!');
        $this->command->newLine();
        $this->command->table(
            ['Name', 'Email', 'Password'],
            array_map(fn($u) => [$u->name, $u->email, 'Test@1234'], $affiliates)
        );
        $this->command->newLine();

        $totalPaid       = Payment::where('status', 'success')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('amount');
        $totalComm       = AffiliateCommission::whereNotNull('reference_type')
            ->where('reference_type', '!=', '')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('amount');
        $gst             = round($totalPaid * 18 / 118, 2);
        $afterGst        = round($totalPaid - $gst, 2);
        $gateway         = round($totalPaid * 2 / 100, 2);
        $profit          = round($afterGst - $totalComm - $gateway, 2);

        $this->command->info("📊 Expected 30-Day Dashboard Values:");
        $this->command->line("   Revenue (30d)   : ₹{$totalPaid}");
        $this->command->line("   GST deducted    : ₹{$gst}");
        $this->command->line("   After GST       : ₹{$afterGst}");
        $this->command->line("   Commission (30d): ₹{$totalComm}");
        $this->command->line("   Gateway (2%)    : ₹{$gateway}");
        $this->command->line("   ✅ Net Profit   : ₹{$profit}");
        $this->command->newLine();
        $this->command->comment('To remove test data: delete users with email ending in "' . self::EMAIL_DOMAIN . '"');
    }
}
