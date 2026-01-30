<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\LookupMaster;
use App\Models\LookupValue;

class LookupSeeder extends Seeder
{
    public function run()
    {
        // Define Masters
        $masters = [
            ['code' => 'USER_STATUS', 'name' => 'حالة المستخدم', 'description' => 'Status of user accounts'],
            ['code' => 'USER_TYPE', 'name' => 'نوع المستخدم', 'description' => 'Type of user account'],
            ['code' => 'SCHOOL_TYPE', 'name' => 'نوع المدرسة', 'description' => 'Type of educational institution'],
            ['code' => 'SCHOOL_LEVEL', 'name' => 'المرحلة التعليمية', 'description' => 'Educational level of the school'],
            ['code' => 'GENDER', 'name' => 'الجنس', 'description' => 'Gender options'],
            ['code' => 'BILLING_CYCLE', 'name' => 'دورة الفوترة', 'description' => 'Billing frequency'],
            ['code' => 'SUBSCRIPTION_STATUS', 'name' => 'حالة الاشتراك', 'description' => 'Current state of subscription'],
            ['code' => 'INVOICE_STATUS', 'name' => 'حالة الفاتورة', 'description' => 'Payment status of invoices'],
            ['code' => 'PAYMENT_METHOD', 'name' => 'طريقة الدفع', 'description' => 'Methods of payment'],
            ['code' => 'PAYMENT_STATUS', 'name' => 'حالة الدفع', 'description' => 'Status of payment transactions'],
            ['code' => 'ITEM_TYPE', 'name' => 'نوع البند', 'description' => 'Type of invoice item'],
            ['code' => 'TRANSACTION_TYPE', 'name' => 'نوع العملية', 'description' => 'Type of financial transaction'],
            ['code' => 'COMMISSION_STATUS', 'name' => 'حالة العمولة', 'description' => 'Status of commission payout'],
            ['code' => 'CARD_TYPE', 'name' => 'نوع البطاقة', 'description' => 'Type of access/photo card'],
            ['code' => 'CARD_STATUS', 'name' => 'حالة البطاقة', 'description' => 'Status of the card'],
            ['code' => 'NOTIFICATION_TYPE', 'name' => 'نوع الإشعار', 'description' => 'Category of notification'],
            ['code' => 'SETTING_TYPE', 'name' => 'نوع الإعداد', 'description' => 'Data type for settings'],
        ];

        foreach ($masters as $masterData) {
            $master = LookupMaster::updateOrCreate(
                ['code' => $masterData['code']],
                ['name' => $masterData['name'], 'description' => $masterData['description']]
            );
            
            $values = $this->getValuesForMaster($masterData['code']);
            foreach ($values as $valueData) {
                $valueData['is_active'] = true;
                $master->values()->updateOrCreate(
                    ['code' => $valueData['code']],
                    $valueData
                );
            }
        }
    }

    private function getValuesForMaster($code)
    {
        switch ($code) {
            case 'USER_STATUS':
                return [
                    ['code' => 'ACTIVE', 'name' => 'نشط', 'description' => 'Account is active'],
                    ['code' => 'PENDING', 'name' => 'قيد المراجعة', 'description' => 'Account pending verification'],
                    ['code' => 'SUSPENDED', 'name' => 'موقوف', 'description' => 'Account suspended'],
                    ['code' => 'INACTIVE', 'name' => 'غير نشط', 'description' => 'Account deactivated'],
                ];
            case 'USER_TYPE':
                return [
                    ['code' => 'SUPER_ADMIN', 'name' => 'مدير النظام'],
                    ['code' => 'STUDIO_OWNER', 'name' => 'صاحب استوديو'],
                    ['code' => 'SCHOOL_OWNER', 'name' => 'صاحب مدرسة'],
                    ['code' => 'CUSTOMER', 'name' => 'عميل'],
                    ['code' => 'EMPLOYEE', 'name' => 'موظف'],
                ];
            case 'SCHOOL_TYPE':
                return [
                    ['code' => 'PUBLIC', 'name' => 'حكومية'],
                    ['code' => 'PRIVATE', 'name' => 'أهلية'],
                    ['code' => 'INTERNATIONAL', 'name' => 'عالمية'],
                ];
            case 'SCHOOL_LEVEL':
                return [
                    ['code' => 'KINDERGARTEN', 'name' => 'روضة'],
                    ['code' => 'PRIMARY', 'name' => 'ابتدائي'],
                    ['code' => 'MIDDLE', 'name' => 'إعدادي'],
                    ['code' => 'HIGH', 'name' => 'ثانوي'],
                    ['code' => 'UNIVERSITY', 'name' => 'جامعي'],
                ];
            case 'GENDER':
                return [
                    ['code' => 'MALE', 'name' => 'ذكر'],
                    ['code' => 'FEMALE', 'name' => 'أنثى'],
                ];
            case 'BILLING_CYCLE':
                return [
                    ['code' => 'MONTHLY', 'name' => 'شهري'],
                    ['code' => 'YEARLY', 'name' => 'سنوي'],
                ];
            case 'SUBSCRIPTION_STATUS':
                return [
                    ['code' => 'ACTIVE', 'name' => 'نشط'],
                    ['code' => 'EXPIRED', 'name' => 'منتهي'],
                    ['code' => 'CANCELLED', 'name' => 'ملغي'],
                ];
            case 'INVOICE_STATUS':
                return [
                    ['code' => 'DRAFT', 'name' => 'مسودة'],
                    ['code' => 'ISSUED', 'name' => 'صادرة'],
                    ['code' => 'PAID', 'name' => 'مدفوعة'],
                    ['code' => 'OVERDUE', 'name' => 'متأخرة'],
                    ['code' => 'CANCELLED', 'name' => 'ملغاة'],
                ];
            case 'PAYMENT_METHOD':
                return [
                    ['code' => 'CREDIT_CARD', 'name' => 'بطاقة ائتمان'],
                    ['code' => 'BANK_TRANSFER', 'name' => 'تحويل بنكي'],
                    ['code' => 'CASH', 'name' => 'نقدي'],
                    ['code' => 'MADA', 'name' => 'مدى'],
                    ['code' => 'APPLE_PAY', 'name' => 'Apple Pay'],
                ];
            case 'PAYMENT_STATUS':
                return [
                    ['code' => 'PENDING', 'name' => 'قيد الانتظار'],
                    ['code' => 'COMPLETED', 'name' => 'مكتمل'],
                    ['code' => 'FAILED', 'name' => 'فشل'],
                    ['code' => 'REFUNDED', 'name' => 'مسترجع'],
                ];
            case 'ITEM_TYPE':
                return [
                    ['code' => 'SUBSCRIPTION', 'name' => 'اشتراك'],
                    ['code' => 'STORAGE', 'name' => 'مساحة تخزين'],
                ];
            case 'TRANSACTION_TYPE':
                return [
                    ['code' => 'SALE', 'name' => 'بيع'],
                    ['code' => 'REFUND', 'name' => 'استرجاع'],
                    ['code' => 'COMMISSION', 'name' => 'عمولة'],
                ];
            case 'COMMISSION_STATUS':
                return [
                    ['code' => 'PENDING', 'name' => 'قيد الانتظار'],
                    ['code' => 'PAID', 'name' => 'مدفوعة'],
                ];
            case 'CARD_TYPE':
                return [
                    ['code' => 'STANDARD', 'name' => 'عادية'],
                    ['code' => 'PREMIUM', 'name' => 'مميزة'],
                    ['code' => 'SUBSCRIBER', 'name' => 'مشترك'],
                ];
            case 'CARD_STATUS':
                return [
                    ['code' => 'ACTIVE', 'name' => 'نشطة'],
                    ['code' => 'INACTIVE', 'name' => 'غير نشطة'],
                    ['code' => 'EXPIRED', 'name' => 'منتهية'],
                ];
            case 'NOTIFICATION_TYPE':
                return [
                    ['code' => 'INFO', 'name' => 'معلومة'],
                    ['code' => 'WARNING', 'name' => 'تحذير'],
                    ['code' => 'ERROR', 'name' => 'خطأ'],
                    ['code' => 'SUCCESS', 'name' => 'نجاح'],
                    ['code' => 'WELCOME', 'name' => 'ترحيب'],
                ];
            case 'SETTING_TYPE':
                return [
                    ['code' => 'STRING', 'name' => 'نص'],
                    ['code' => 'BOOLEAN', 'name' => 'منطقي'],
                    ['code' => 'JSON', 'name' => 'JSON'],
                    ['code' => 'INTEGER', 'name' => 'رقم'],
                ];
            default:
                return [];
        }
    }
}

