<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SystemSeeder::class,
            RolePermissionSeeder::class,
            LookupSeeder::class,
            PlanSeeder::class,
            AdminSeeder::class,
            TestDataSeeder::class, // Comprehensive test data
        ]);
    }
}
