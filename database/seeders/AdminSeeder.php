<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Identity\Models\Account;
use App\Domain\Identity\Models\Role;
use App\Domain\Identity\Models\Permission;
use App\Domain\Shared\Models\LookupValue;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Get ACTIVE status for account
        $activeStatus = LookupValue::whereHas('master', function($q) {
            $q->where('code', 'ACCOUNT_STATUS');
        })->where('code', 'ACTIVE')->first();

        if (!$activeStatus) {
            $this->command->error('ACCOUNT_STATUS lookup not found. Please run LookupSeeder first.');
            return;
        }

        // Create Admin Role
        $adminRole = Role::firstOrCreate(
            ['name' => 'Administrator'],
            [
                'description' => 'Full system access',
                'is_system' => true
            ]
        );

        // Create Permissions
        $permissions = [
            ['name' => 'Manage Users', 'resource_type' => 'Account', 'action' => 'manage', 'description' => 'Create, edit, delete users'],
            ['name' => 'Manage Roles', 'resource_type' => 'Role', 'action' => 'manage', 'description' => 'Create, edit, delete roles'],
            ['name' => 'Manage Permissions', 'resource_type' => 'Permission', 'action' => 'manage', 'description' => 'Assign permissions'],
            ['name' => 'Manage Studios', 'resource_type' => 'Studio', 'action' => 'manage', 'description' => 'Manage studio profiles'],
            ['name' => 'Manage Schools', 'resource_type' => 'School', 'action' => 'manage', 'description' => 'Manage school profiles'],
            ['name' => 'Manage Subscriptions', 'resource_type' => 'Subscription', 'action' => 'manage', 'description' => 'Manage subscriptions'],
            ['name' => 'Manage Invoices', 'resource_type' => 'Invoice', 'action' => 'manage', 'description' => 'Manage invoices'],
            ['name' => 'Manage Payments', 'resource_type' => 'Payment', 'action' => 'manage', 'description' => 'Process payments'],
            ['name' => 'View Reports', 'resource_type' => 'Report', 'action' => 'view', 'description' => 'Access system reports'],
            ['name' => 'Manage Settings', 'resource_type' => 'Setting', 'action' => 'manage', 'description' => 'Configure system settings'],
        ];

        $permissionIds = [];
        foreach ($permissions as $permData) {
            $permission = Permission::firstOrCreate(
                ['name' => $permData['name']],
                [
                    'resource_type' => $permData['resource_type'],
                    'action' => $permData['action'],
                    'description' => $permData['description']
                ]
            );
            $permissionIds[] = $permission->permission_id;
        }

        // Attach all permissions to admin role
        $adminRole->permissions()->sync($permissionIds);

        // Create Admin Account
        $admin = Account::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'full_name' => 'System Administrator',
                'phone' => '+966500000000',
                'account_status_id' => $activeStatus->lookup_value_id,
                'password_hash' => bcrypt('password'), // Change this in production!
            ]
        );

        // Attach admin role to admin account
        $admin->roles()->syncWithoutDetaching([$adminRole->role_id]);

        $this->command->info('Admin account created successfully!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
        $this->command->warn('⚠️  Please change the password after first login!');
    }
}
