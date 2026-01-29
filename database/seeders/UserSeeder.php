<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\LookupValue;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $activeStatus = LookupValue::where('code', 'ACTIVE')->whereHas('master', function($q) {
            $q->where('code', 'USER_STATUS');
        })->first();

        $superAdminType = LookupValue::where('code', 'SUPER_ADMIN')->first();
        $studioOwnerType = LookupValue::where('code', 'STUDIO_OWNER')->first();
        $schoolOwnerType = LookupValue::where('code', 'SCHOOL_OWNER')->first();

        // 1. Super Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'phone' => '+966500000000',
                'user_status_id' => $activeStatus->lookup_value_id,
                'user_type_id' => $superAdminType->lookup_value_id,
                'is_active' => true,
                'email_verified' => true,
                'phone_verified' => true,
            ]
        );
        $adminRole = Role::where('name', 'Administrator')->first();
        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching([$adminRole->role_id]);
        }

        // 2. Test Studio Owner
        $studioUser = User::updateOrCreate(
            ['email' => 'studio@example.com'],
            [
                'name' => 'Test Studio',
                'username' => 'studio_test',
                'password' => Hash::make('password'),
                'phone' => '+966511111111',
                'user_status_id' => $activeStatus->lookup_value_id,
                'user_type_id' => $studioOwnerType->lookup_value_id,
                'is_active' => true,
                'email_verified' => true,
            ]
        );
        $studioRole = Role::where('name', 'Studio Owner')->first();
        if ($studioRole) {
            $studioUser->roles()->syncWithoutDetaching([$studioRole->role_id]);
        }

        // 3. Test School Owner
        $schoolUser = User::updateOrCreate(
            ['email' => 'school@example.com'],
            [
                'name' => 'Test School',
                'username' => 'school_test',
                'password' => Hash::make('password'),
                'phone' => '+966522222222',
                'user_status_id' => $activeStatus->lookup_value_id,
                'user_type_id' => $schoolOwnerType->lookup_value_id,
                'is_active' => true,
                'email_verified' => true,
            ]
        );
        $schoolRole = Role::where('name', 'School Owner')->first();
        if ($schoolRole) {
            $schoolUser->roles()->syncWithoutDetaching([$schoolRole->role_id]);
        }
    }
}
