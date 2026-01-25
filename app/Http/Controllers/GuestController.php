<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Models\Account;
use App\Domain\Shared\Models\LookupValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    /**
     * إنشاء حساب زائر تلقائيًا
     */
    public function createGuestAccount(Request $request)
    {
        // الحصول على حالة GUEST من lookup_values
        $guestStatus = LookupValue::whereHas('master', function($q) {
            $q->where('code', 'ACCOUNT_STATUS');
        })->where('code', 'GUEST')->first();

        if (!$guestStatus) {
            return response()->json([
                'error' => 'Guest status not found. Please run seeders.'
            ], 500);
        }

        // إنشاء حساب زائر بمعلومات عشوائية
        $guestUsername = 'guest_' . Str::random(8);
        $guestPhone = '+966' . str_pad(random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        
        $guestAccount = Account::create([
            'username' => $guestUsername,
            'email' => null, // البريد الإلكتروني اختياري
            'full_name' => 'زائر',
            'phone' => $guestPhone,
            'account_status_id' => $guestStatus->lookup_value_id,
            'password_hash' => bcrypt(Str::random(32)), // كلمة مرور عشوائية
            'email_verified' => false,
            'phone_verified' => false,
        ]);

        // يمكنك هنا تسجيل دخول الزائر تلقائيًا أو إرجاع معلومات الحساب
        // للتبسيط، سنرجع معلومات الحساب
        
        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء حساب زائر بنجاح',
            'account' => [
                'account_id' => $guestAccount->account_id,
                'username' => $guestAccount->username,
                'full_name' => $guestAccount->full_name,
            ]
        ]);
    }

    /**
     * عرض صفحة الزائر
     */
    public function guestDashboard()
    {
        return view('guest.dashboard');
    }
}
