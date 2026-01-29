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

        // 1. Super Admin
        $superAdminType = LookupValue::where('code', 'SUPER_ADMIN')->first();
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'مدير النظام',
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
        $role = Role::where('name', 'super_admin')->first();
        if ($role) $admin->roles()->syncWithoutDetaching([$role->role_id]);

        // 2. Studio Owner
        $studioType = LookupValue::where('code', 'STUDIO_OWNER')->first();
        $studio = User::updateOrCreate(
            ['email' => 'studio@example.com'],
            [
                'name' => 'صاحب الاستوديو',
                'username' => 'studio',
                'password' => Hash::make('password'),
                'phone' => '+966511111111',
                'user_status_id' => $activeStatus->lookup_value_id,
                'user_type_id' => $studioType->lookup_value_id,
                'is_active' => true,
                'email_verified' => true,
            ]
        );
        $role = Role::where('name', 'studio_owner')->first();
        if ($role) $studio->roles()->syncWithoutDetaching([$role->role_id]);

        // 3. School Owner
        $schoolType = LookupValue::where('code', 'SCHOOL_OWNER')->first();
        $school = User::updateOrCreate(
            ['email' => 'school@example.com'],
            [
                'name' => 'صاحب المدرسة',
                'username' => 'school',
                'password' => Hash::make('password'),
                'phone' => '+966522222222',
                'user_status_id' => $activeStatus->lookup_value_id,
                'user_type_id' => $schoolType->lookup_value_id,
                'is_active' => true,
                'email_verified' => true,
            ]
        );
        $role = Role::where('name', 'school_owner')->first();
        if ($role) $school->roles()->syncWithoutDetaching([$role->role_id]);

        // 4. Customer
        $customerType = LookupValue::where('code', 'CUSTOMER')->first();
        $customerUser = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'عميل تجريبي',
                'username' => 'customer',
                'password' => Hash::make('password'),
                'phone' => '+966533333333',
                'user_status_id' => $activeStatus->lookup_value_id,
                'user_type_id' => $customerType->lookup_value_id,
                'is_active' => true,
                'email_verified' => true,
            ]
        );
        $role = Role::where('name', 'customer')->first();
        if ($role) $customerUser->roles()->syncWithoutDetaching([$role->role_id]);

        // 5. Employee
        $employeeType = LookupValue::where('code', 'EMPLOYEE')->first();
        $employee = User::updateOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'موظف تجريبي',
                'username' => 'employee',
                'password' => Hash::make('password'),
                'phone' => '+966544444444',
                'user_status_id' => $activeStatus->lookup_value_id,
                'user_type_id' => $employeeType->lookup_value_id,
                'is_active' => true,
                'email_verified' => true,
            ]
        );
        $role = Role::where('name', 'employee')->first();
        if ($role) $employee->roles()->syncWithoutDetaching([$role->role_id]);
    }
}
