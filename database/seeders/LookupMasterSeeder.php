<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LookupMasterSeeder extends Seeder
{
    public function run(): void
    {
        $masters = [
            ['code' => 'USER_STATUS', 'name' => 'حالة المستخدم', 'description' => 'حالات المستخدمين في النظام'],
            ['code' => 'SCHOOL_TYPE', 'name' => 'نوع المدرسة', 'description' => 'أنواع المدارس'],
            ['code' => 'SCHOOL_LEVEL', 'name' => 'المرحلة الدراسية', 'description' => 'المراحل الدراسية'],
        ];

        foreach ($masters as $master) {
            DB::table('lookup_masters')->updateOrInsert(
                ['code' => $master['code']],
                [
                    'name' => $master['name'],
                    'description' => $master['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
