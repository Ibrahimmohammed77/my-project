<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 0. Clear Cache
        \Illuminate\Support\Facades\Cache::flush();
        
        // Roles and Permissions now handled in RolePermissionSeeder
    }
}
