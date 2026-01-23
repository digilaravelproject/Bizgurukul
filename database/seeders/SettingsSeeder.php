<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'BizGurukul',
            'referral_system_active' => '1',
            'referral_commission_amount' => '500',
            'referral_cookie_expiry_days' => '30',
            'min_payout_amount' => '1000',
            'currency_symbol' => '₹',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        $this->command->info('Settings seeded and cache cleared successfully!');
    }
}
