<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupValueSeeder extends Seeder
{
    public function run(): void
    {
        // Get master IDs
        $userStatusMaster = DB::table('lookup_masters')->where('code', 'USER_STATUS')->first();
        $schoolTypeMaster = DB::table('lookup_masters')->where('code', 'SCHOOL_TYPE')->first();
        $schoolLevelMaster = DB::table('lookup_masters')->where('code', 'SCHOOL_LEVEL')->first();

        // User Status values
        $userStatuses = [
            ['code' => 'ACTIVE', 'name' => 'نشط', 'description' => 'حساب نشط'],
            ['code' => 'INACTIVE', 'name' => 'غير نشط', 'description' => 'حساب غير نشط'],
            ['code' => 'SUSPENDED', 'name' => 'موقوف', 'description' => 'حساب موقوف'],
            ['code' => 'PENDING', 'name' => 'قيد المراجعة', 'description' => 'حساب قيد المراجعة'],
        ];

        // School Type values
        $schoolTypes = [
            ['code' => 'PUBLIC', 'name' => 'حكومي', 'description' => 'مدرسة حكومية'],
            ['code' => 'PRIVATE', 'name' => 'خاص', 'description' => 'مدرسة خاصة'],
            ['code' => 'INTERNATIONAL', 'name' => 'دولي', 'description' => 'مدرسة دولية'],
        ];

        // School Level values
        $schoolLevels = [
            ['code' => 'PRIMARY', 'name' => 'ابتدائي', 'description' => 'المرحلة الابتدائية'],
            ['code' => 'MIDDLE', 'name' => 'متوسط', 'description' => 'المرحلة المتوسطة'],
            ['code' => 'HIGH', 'name' => 'ثانوي', 'description' => 'المرحلة الثانوية'],
            ['code' => 'KINDERGARTEN', 'name' => 'روضة', 'description' => 'مرحلة الروضة'],
        ];

        // Insert user statuses
        foreach ($userStatuses as $status) {
            DB::table('lookup_values')->updateOrInsert(
                [
                    'lookup_master_id' => $userStatusMaster->lookup_master_id,
                    'code' => $status['code']
                ],
                [
                    'name' => $status['name'],
                    'description' => $status['description'],
                    'is_active' => 1,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Insert school types
        foreach ($schoolTypes as $type) {
            DB::table('lookup_values')->updateOrInsert(
                [
                    'lookup_master_id' => $schoolTypeMaster->lookup_master_id,
                    'code' => $type['code']
                ],
                [
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'is_active' => 1,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Insert school levels
        foreach ($schoolLevels as $level) {
            DB::table('lookup_values')->updateOrInsert(
                [
                    'lookup_master_id' => $schoolLevelMaster->lookup_master_id,
                    'code' => $level['code']
                ],
                [
                    'name' => $level['name'],
                    'description' => $level['description'],
                    'is_active' => 1,
                    'sort_order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
