<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => 'SKILLS PEHLE',
            'site_description' => 'Learn and Earn',
            'affiliate_module_enabled' => true,
            'course_selling_enabled' => true,
            'currency_symbol' => '₹',
            'currency_code' => 'INR',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
