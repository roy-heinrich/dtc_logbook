<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            ['name' => 'view_dashboard', 'display_name' => 'View Dashboard', 'description' => 'Can view admin dashboard'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'description' => 'Can create, edit, and delete users'],
            ['name' => 'manage_activities', 'display_name' => 'Manage Activities', 'description' => 'Can manage activity records'],
            ['name' => 'view_login_logs', 'display_name' => 'View Login Logs', 'description' => 'Can view login logs'],
            ['name' => 'export_data', 'display_name' => 'Export Data', 'description' => 'Can export data'],
            ['name' => 'manage_admins', 'display_name' => 'Manage Admins', 'description' => 'Can create, edit, and delete admins'],
            ['name' => 'manage_roles', 'display_name' => 'Manage Roles', 'description' => 'Can create, edit, and delete roles'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        // Create Super Admin Role
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Administrator',
                'description' => 'Has full access to all system features',
            ]
        );

        // Super admin gets all permissions
        $superAdmin->permissions()->sync(Permission::all()->pluck('id'));

        // Create Admin Role
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Can manage most features except admin and role management',
            ]
        );

        // Admin gets limited permissions
        $adminPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'manage_users',
            'manage_activities',
            'view_login_logs',
            'export_data',
        ])->pluck('id');
        $admin->permissions()->sync($adminPermissions);

        // Create Manager Role
        $manager = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Can view and manage users and activities',
            ]
        );

        // Manager gets basic permissions
        $managerPermissions = Permission::whereIn('name', [
            'view_dashboard',
            'manage_users',
            'manage_activities',
        ])->pluck('id');
        $manager->permissions()->sync($managerPermissions);

        // Create Viewer Role
        $viewer = Role::firstOrCreate(
            ['name' => 'viewer'],
            [
                'display_name' => 'Viewer',
                'description' => 'Can only view dashboard and data',
            ]
        );

        // Viewer gets view-only permissions
        $viewerPermissions = Permission::whereIn('name', [
            'view_dashboard',
        ])->pluck('id');
        $viewer->permissions()->sync($viewerPermissions);
    }
}
