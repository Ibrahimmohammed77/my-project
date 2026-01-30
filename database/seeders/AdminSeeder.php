<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // 1. Create Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'password' => Hash::make('password'),
                'phone' => '+966500000000',
                'email_verified_at' => now(),
                "is_active" => true,
            ]
        );
        $superAdmin->user_status_id = \App\Models\LookupValue::where('code', 'ACTIVE')
            ->whereHas('master', fn($q) => $q->where('code', 'USER_STATUS'))
            ->value('lookup_value_id');
        $superAdmin->user_type_id = \App\Models\LookupValue::where('code', 'ADMIN')
            ->whereHas('master', fn($q) => $q->where('code', 'USER_TYPE'))
            ->value('lookup_value_id');
        $superAdmin->save();
        
        $superAdminRole = Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->role_id]);
        }

        // 2. Create Support Admin
        $supportAdmin = User::firstOrCreate(
            ['email' => 'support@admin.com'],
            [
                'name' => 'Support Admin',
                'username' => 'support',
                'password' => Hash::make('password'),
                'phone' => '+966500000001',
                'email_verified_at' => now(),
                "is_active" => true,
            ]
        );
        $supportAdmin->user_status_id = $superAdmin->user_status_id;
        $supportAdmin->user_type_id = $superAdmin->user_type_id;
        $supportAdmin->save();

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $supportAdmin->roles()->syncWithoutDetaching([$adminRole->role_id]);
        }

        $this->command->info('Admins seeded successfully.');
    }
}
