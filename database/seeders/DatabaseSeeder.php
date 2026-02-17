<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call() array ke andar saare seeders ke naam likhein
        $this->call([
            StateSeeder::class,            // 1. States
            RoleAndUserSeeder::class,      // 2. Roles & Base Users
            CategorySeeder::class,         // 3. Categories
            SettingsSeeder::class,         // 4. Global Settings
        ]);
    }
}
