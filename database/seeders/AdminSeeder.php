<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get super admin role
        $superAdminRole = Role::where('name', 'super_admin')->first();

        // Create super admin
        Admin::firstOrCreate(
            ['email' => 'admin@dtclogbook.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $superAdminRole?->id,
                'is_active' => true,
            ]
        );

        // Create a regular admin for testing
        $adminRole = Role::where('name', 'admin')->first();
        
        Admin::firstOrCreate(
            ['email' => 'admin2@dtclogbook.com'],
            [
                'name' => 'Regular Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $adminRole?->id,
                'is_active' => true,
            ]
        );
    }
}
