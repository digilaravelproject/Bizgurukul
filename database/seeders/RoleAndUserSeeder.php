<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Spatie Permission Cache Reset karein (Zaroori hai)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Roles Create Karein (Admin aur Student)
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);

        // 3. ADMIN User Create Karein & Role Assign Karein
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'], // Is email se check karega
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'is_active' => 1,
                // Baaki fields nullable hain migration me, to error nahi aayega
            ]
        );

        // Admin role assign karein
        $adminUser->assignRole($adminRole);


        // 4. STUDENT User Create Karein & Role Assign Karein
        $studentUser = User::firstOrCreate(
            ['email' => 'student@student.com'], // Is email se check karega
            [
                'name' => 'Demo Student',
                'password' => Hash::make('12345678'),
                'is_active' => 1,
            ]
        );

        // Student role assign karein
        $studentUser->assignRole($studentRole);
    }
}
