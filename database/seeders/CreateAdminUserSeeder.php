<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. User Create Karein
        // firstOrCreate use karein taaki duplicate error na aaye
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Check karega is email se
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123456'),
                'is_active' => 1
            ]
        );

        // 2. Role Create Karein
        $role = Role::firstOrCreate(['name' => 'Admin']);

        // 3. Permissions Sync Karein (YEH LINE CHANGE KI HAI)
        // Hum saari permissions database se utha kar role ko de rahe hain
        $permissions = Permission::all();

        $role->syncPermissions($permissions);

        // 4. User ko Role Assign Karein
        $user->assignRole([$role->id]);
    }
}
