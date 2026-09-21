<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Bundle;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CreateStudentUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-student 
                            {--name= : Full name of user} 
                            {--email= : User email} 
                            {--dob= : Date of birth (YYYY-MM-DD or DD-MM-YYYY)} 
                            {--password= : Login password (defaults to Omkar@1998)} 
                            {--bundle=Finance Skills : Bundle name or ID} 
                            {--sponsor= : Sponsor name, email or referral code}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a student user with bundle access and sponsor attribution';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name') ?: 'Omkar Vikrant Kute';
        $email = strtolower(trim($this->option('email') ?: 'Omkarkute54@gmail.com'));
        $dobRaw = $this->option('dob') ?: '10-08-1998';
        $password = $this->option('password') ?: 'Omkar@1998';
        $bundleQuery = $this->option('bundle') ?: 'Finance Skills';
        $sponsorQuery = $this->option('sponsor') ?: 'Vinay Shrivardhankar';

        // Format DOB to Y-m-d
        $dob = date('Y-m-d', strtotime($dobRaw));

        $this->info("Creating/Updating Student User...");
        $this->line("Name: {$name}");
        $this->line("Email: {$email}");
        $this->line("DOB: {$dob}");
        $this->line("Password: {$password}");

        // 1. Find Sponsor
        $sponsor = User::where('name', 'like', "%{$sponsorQuery}%")
            ->orWhere('email', 'like', "%{$sponsorQuery}%")
            ->orWhere('referral_code', $sponsorQuery)
            ->first();

        // Fallback search if Vinay vs Vijay
        if (!$sponsor && stripos($sponsorQuery, 'Shrivardhankar') !== false) {
            $sponsor = User::where('name', 'like', '%Shrivardhankar%')
                ->orWhere('email', 'like', '%shrivardhankar%')
                ->first();
        }

        if ($sponsor) {
            $this->info("Found Sponsor: {$sponsor->name} (Email: {$sponsor->email}, ID: {$sponsor->id}, Ref: {$sponsor->referral_code})");
        } else {
            $this->warn("Warning: Sponsor '{$sponsorQuery}' not found in database. User will be created without sponsor.");
        }

        // 2. Find Bundle
        $bundle = is_numeric($bundleQuery)
            ? Bundle::find($bundleQuery)
            : Bundle::where('title', 'like', "%{$bundleQuery}%")->first();

        if (!$bundle) {
            $this->error("Bundle '{$bundleQuery}' not found in database.");
            return 1;
        }

        $this->info("Found Bundle: {$bundle->title} (ID: {$bundle->id}, Preference: {$bundle->preference_index})");

        // 3. Create/Update User & Assign Bundle Access
        DB::beginTransaction();
        try {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'dob' => $dob,
                    'password' => Hash::make($password),
                    'is_active' => true,
                    'kyc_status' => 'not_submitted',
                    'referred_by' => $sponsor?->id,
                ]
            );

            // Assign Student Role
            $studentRole = Role::firstOrCreate(['name' => 'Student']);
            if (!$user->hasRole('Student')) {
                $user->assignRole($studentRole);
            }

            // Assign Bundle Access via Payment
            $amount = $bundle->affiliate_price ?? $bundle->final_price ?? 15999.00;
            $payment = Payment::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'bundle_id' => $bundle->id,
                    'status' => 'success',
                ],
                [
                    'paymentable_type' => Bundle::class,
                    'paymentable_id' => $bundle->id,
                    'amount' => $amount,
                    'subtotal' => $amount,
                    'discount_amount' => 0,
                    'tax_amount' => round($amount * 18 / 118, 2),
                    'total_amount' => $amount,
                    'razorpay_order_id' => 'manual_order_' . time(),
                    'razorpay_payment_id' => 'manual_pay_' . time(),
                ]
            );

            DB::commit();

            $this->info("--------------------------------------------------");
            $this->info("SUCCESS! User created & enrolled successfully.");
            $this->info("User ID: {$user->id}");
            $this->info("Email: {$user->email}");
            $this->info("Referral Code: {$user->referral_code}");
            $this->info("Sponsor: " . ($user->referrer ? $user->referrer->name : 'None'));
            $this->info("Unlocked Bundles Count: " . count($user->unlockedBundleIds()));
            $this->info("--------------------------------------------------");

            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("Failed to create student: " . $e->getMessage());
            return 1;
        }
    }
}
