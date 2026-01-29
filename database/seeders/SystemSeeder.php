<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\LookupValue;

class SystemSeeder extends Seeder
{
    public function run()
    {
        $this->seedPermissions();
        $this->seedRoles();
        $this->seedPlans();
        $this->seedSettings();
    }

    private function seedPermissions()
    {
        $permissions = [
            ['name' => 'Manage Users', 'resource_type' => 'User', 'action' => 'manage', 'description' => 'Create, edit, delete users'],
            ['name' => 'Manage Roles', 'resource_type' => 'Role', 'action' => 'manage', 'description' => 'Create, edit, delete roles'],
            ['name' => 'Manage Permissions', 'resource_type' => 'Permission', 'action' => 'manage', 'description' => 'Assign permissions'],
            ['name' => 'Manage Studios', 'resource_type' => 'Studio', 'action' => 'manage', 'description' => 'Manage studio profiles'],
            ['name' => 'Manage Schools', 'resource_type' => 'School', 'action' => 'manage', 'description' => 'Manage school profiles'],
            ['name' => 'Manage Subscriptions', 'resource_type' => 'Subscription', 'action' => 'manage', 'description' => 'Manage subscriptions'],
            ['name' => 'Manage Invoices', 'resource_type' => 'Invoice', 'action' => 'manage', 'description' => 'Manage invoices'],
            ['name' => 'Manage Payments', 'resource_type' => 'Payment', 'action' => 'manage', 'description' => 'Process payments'],
            ['name' => 'View Reports', 'resource_type' => 'Report', 'action' => 'view', 'description' => 'Access system reports'],
            ['name' => 'Manage Settings', 'resource_type' => 'Setting', 'action' => 'manage', 'description' => 'Configure system settings'],
            ['name' => 'Upload Photos', 'resource_type' => 'Photo', 'action' => 'upload', 'description' => 'Upload photos to albums'],
            ['name' => 'Manage Albums', 'resource_type' => 'Album', 'action' => 'manage', 'description' => 'Create and edit albums'],
            ['name' => 'Manage Cards', 'resource_type' => 'Card', 'action' => 'manage', 'description' => 'Manage access cards'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['name' => $perm['name']], $perm);
        }
    }

    private function seedRoles()
    {
        // Administrator
        $admin = Role::updateOrCreate(
            ['name' => 'Administrator'],
            ['description' => 'Full system access', 'is_system' => true]
        );
        $admin->permissions()->sync(Permission::all()->pluck('permission_id'));

        // Studio Owner
        $studioOwner = Role::updateOrCreate(
            ['name' => 'Studio Owner'],
            ['description' => 'Manage studio and own albums', 'is_system' => true]
        );
        $studioOwnerPermissions = Permission::whereIn('resource_type', ['Studio', 'Album', 'Photo', 'Card'])->pluck('permission_id');
        $studioOwner->permissions()->sync($studioOwnerPermissions);

        // School Owner
        $schoolOwner = Role::updateOrCreate(
            ['name' => 'School Owner'],
            ['description' => 'Manage school profiles', 'is_system' => true]
        );
        $schoolOwnerPermissions = Permission::whereIn('resource_type', ['School', 'Album', 'Photo'])->pluck('permission_id');
        $schoolOwner->permissions()->sync($schoolOwnerPermissions);

        // Customer
        Role::updateOrCreate(
            ['name' => 'Customer'],
            ['description' => 'End user who views and owns photos', 'is_system' => true]
        );
    }

    private function seedPlans()
    {
        $monthlyCycle = LookupValue::where('code', 'MONTHLY')->first();

        $plans = [
            [
                'name' => 'Basic Studio',
                'description' => 'For small independent photographers',
                'storage_limit' => 50, // GB
                'price_monthly' => 19.99,
                'price_yearly' => 199.99,
                'max_albums' => 50,
                'max_cards' => 500,
                'max_users' => 2,
                'max_storage_libraries' => 1,
                'billing_cycle_id' => $monthlyCycle->lookup_value_id,
                'is_active' => true,
            ],
            [
                'name' => 'Professional Studio',
                'description' => 'For established photography businesses',
                'storage_limit' => 250, // GB
                'price_monthly' => 49.99,
                'price_yearly' => 499.99,
                'max_albums' => 500,
                'max_cards' => 5000,
                'max_users' => 10,
                'max_storage_libraries' => 5,
                'billing_cycle_id' => $monthlyCycle->lookup_value_id,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['name' => $plan['name']], $plan);
        }
    }

    private function seedSettings()
    {
        $stringType = LookupValue::where('code', 'STRING')->first();
        
        $settings = [
            [
                'setting_key' => 'site_name',
                'setting_value' => 'Albums Platform',
                'setting_type_id' => $stringType->lookup_value_id,
                'description' => 'The name of the platform',
                'is_public' => true,
            ],
            [
                'setting_key' => 'support_email',
                'setting_value' => 'support@example.com',
                'setting_type_id' => $stringType->lookup_value_id,
                'description' => 'Customer support email address',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['setting_key' => $setting['setting_key']], $setting);
        }
    }
}
