<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Role;
use App\Models\School;
use App\Models\StorageLibrary;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Roles Exist
        $schoolRole = Role::firstOrCreate(['name' => 'school-owner']);

        // 2. Create School Owner User
        $user = User::firstOrCreate(
            ['email' => 'school@example.com'],
            [
                'name' => 'مدرسة الأمل النموذجية',
                'username' => 'school_admin',
                'password' => Hash::make('password123'),
                'phone' => '0777777777',
                'email_verified_at' => now(),
            ]
        );

        $user->roles()->syncWithoutDetaching([$schoolRole->role_id]);

        // 3. Create/Get Plan
        $plan = Plan::firstOrCreate(
            ['name' => 'خطة المدارس الأساسية'],
            [
                'description' => 'خطة مخصصة للمؤسسات التعليمية',
                'storage_limit' => 1024 * 1024 * 5, // 5GB
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_albums' => 50,
                'max_cards' => 1000,
                'max_users' => 10,
                'max_storage_libraries' => 1,
                'is_active' => true,
            ]
        );

        // 4. Create Active Subscription
        Subscription::updateOrCreate(
            ['user_id' => $user->id, 'plan_id' => $plan->plan_id],
            [
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'renewal_date' => now()->addYear(),
                'auto_renew' => true,
            ]
        );

        // 5. Create School record
        $school = School::updateOrCreate(
            ['user_id' => $user->id],
            [
                'description' => 'مدرسة رائدة في التعليم المتميز',
                'address' => 'شارع الستين، صنعاء',
                'city' => 'صنعاء',
                'settings' => ['theme' => 'light'],
            ]
        );

        // 6. Create Storage Library for the School
        StorageLibrary::updateOrCreate(
            ['school_id' => $school->school_id],
            [
                'user_id' => $user->id,
                'name' => 'مكتبة تخزين المدرسة المركزية',
                'storage_limit' => 1024 * 1024 * 5, // 5GB
            ]
        );

        $this->command->info('School environment seeded successfully! User: school@example.com / pass: password123');
    }
}
