<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Bundle;
use App\Models\Payment;
use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TestSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Demo Student (Based on raj@gmail.com structure)
        $demoUser = User::updateOrCreate(
            ['email' => 'demo_student@example.com'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make('12345678'),
                'mobile' => '9999888877',
                'gender' => 'male',
                'dob' => '2000-01-01',
                'state_id' => 10,
                'is_active' => 1,
                'kyc_status' => 'verified',
            ]
        );

        // Assign Role if Spatie is used
        if (method_exists($demoUser, 'assignRole')) {
            $demoUser->assignRole('Student');
        }

        // 2. Buy a bundle for Demo Student
        $bundle = Bundle::first() ?? Bundle::create(['title' => 'Sample Bundle', 'final_price' => 5000]);

        Payment::updateOrCreate(
            ['user_id' => $demoUser->id, 'bundle_id' => $bundle->id],
            [
                'amount' => $bundle->final_price ?? 5000,
                'status' => 'success',
                'razorpay_order_id' => 'order_' . str()->random(10),
                'razorpay_payment_id' => 'pay_' . str()->random(10),
                'total_amount' => $bundle->final_price ?? 5000,
            ]
        );

        // 3. Create 10 more random users using Factory
        $users = User::factory()->count(10)->create();

        foreach ($users as $user) {
            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Student');
            }

            // Randomly assign bundle purchase to some users
            if (rand(0, 1)) {
                Payment::create([
                    'user_id' => $user->id,
                    'bundle_id' => $bundle->id,
                    'amount' => $bundle->final_price ?? 5000,
                    'status' => 'success',
                    'razorpay_order_id' => 'order_' . str()->random(10),
                    'razorpay_payment_id' => 'pay_' . str()->random(10),
                    'total_amount' => $bundle->final_price ?? 5000,
                ]);
            }

            // 4. Create random commissions for leaderboard testing
            // We'll create commissions for different time periods
            $timePeriods = [
                ['days' => 0, 'label' => 'Today'],
                ['days' => 5, 'label' => 'Last 7 Days'],
                ['days' => 20, 'label' => 'Last 30 Days'],
                ['days' => 60, 'label' => 'Lifetime'],
            ];

            foreach ($timePeriods as $period) {
                // Create 1-3 commissions per user in some periods
                if (rand(0, 5) > 1) {
                    $amount = rand(500, 5000);
                    AffiliateCommission::create([
                        'affiliate_id' => $user->id,
                        'referred_user_id' => $demoUser->id, // Just picking a user
                        'amount' => $amount,
                        'payable_amount' => $amount,
                        'status' => 'paid',
                        'created_at' => Carbon::now()->subDays($period['days']),
                    ]);
                }
            }
        }

        // Give Demo Student some commissions too so they show up on leaderboard
        AffiliateCommission::create([
            'affiliate_id' => $demoUser->id,
            'referred_user_id' => $users->random()->id,
            'amount' => 15000,
            'payable_amount' => 15000,
            'status' => 'paid',
            'created_at' => Carbon::now(),
        ]);

        $this->command->info('TestSystemSeeder completed successfully!');
        $this->command->info('Demo User Email: demo_student@example.com');
        $this->command->info('Password: 12345678');
    }
}
