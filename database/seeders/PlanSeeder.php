<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\LookupValue;

class PlanSeeder extends Seeder
{
    public function run()
    {
        Plan::updateOrCreate(
            ['name' => 'الباقة الأساسية'],
            [
                'description' => 'باقة للمبتدئين وللاستوديوهات الناشئة',
                'storage_limit' => 10 * 1024 * 1024 * 1024, // 10GB
                'price_monthly' => 99.00,
                'price_yearly' => 990.00,
                'is_active' => true,
                'features' => ['دعم فني', 'تخزين سحابي 10 جيجا', 'معالجة صور أساسية']
            ]
        );

        Plan::updateOrCreate(
            ['name' => 'الباقة الاحترافية'],
            [
                'description' => 'باقة متكاملة للاستوديوهات الاحترافية والمدرسية',
                'storage_limit' => 100 * 1024 * 1024 * 1024, // 100GB
                'price_monthly' => 299.00,
                'price_yearly' => 2990.00,
                'is_active' => true,
                'features' => ['دعم فني متقدم 24/7', 'تخزين سحابي 100 جيجا', 'معالجة صور متقدمة', 'تقارير تفصيلية']
            ]
        );

        Plan::updateOrCreate(
            ['name' => 'باقة المؤسسات'],
            [
                'description' => 'باقة مخصصة للشركات الكبيرة والمجمعات التعليمية',
                'storage_limit' => 1024 * 1024 * 1024 * 1024, // 1TB
                'price_monthly' => 999.00,
                'price_yearly' => 9990.00,
                'is_active' => true,
                'features' => ['خادم خاص', 'تخزين سحابي 1 تيرا', 'إدارة مستخدمين متعددة المستويات', 'دعم فني مخصص']
            ]
        );
    }
}
