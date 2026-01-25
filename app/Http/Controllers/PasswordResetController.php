<?php

namespace App\Http\Controllers;

use App\Domain\Identity\Models\Account;
use App\Domain\Shared\Models\LookupValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * عرض نموذج طلب إعادة تعيين كلمة المرور
     */
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * إرسال رمز إعادة التعيين
     */
    public function sendResetCode(Request $request)
    {
        $validated = $request->validate([
            'contact' => 'required|string', // يمكن أن يكون بريد إلكتروني أو رقم هاتف
        ]);

        $contact = $validated['contact'];
        
        // تحديد نوع الاتصال (بريد أو هاتف)
        $contactMethod = filter_var($contact, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        // البحث عن الحساب
        $account = Account::where($contactMethod, $contact)->first();
        
        if (!$account) {
            return back()->withErrors(['contact' => 'لم يتم العثور على حساب بهذا البريد أو رقم الهاتف.']);
        }

        // إنشاء رمز التحقق (6 أرقام)
        $resetCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // حفظ رمز التحقق في قاعدة البيانات
        DB::table('password_resets')->insert([
            'account_id' => $account->account_id,
            'reset_code' => $resetCode,
            'contact_method' => $contactMethod,
            'contact_value' => $contact,
            'expires_at' => now()->addMinutes(15), // صالح لمدة 15 دقيقة
            'is_used' => false,
            'created_at' => now(),
        ]);

        // TODO: إرسال الرمز عبر البريد الإلكتروني أو SMS
        // في الوقت الحالي، سنعرض الرمز في الجلسة للتطوير فقط
        session()->flash('reset_code_sent', true);
        session()->flash('reset_code_debug', $resetCode); // للتطوير فقط - احذف في الإنتاج!
        
        return redirect()->route('password.reset.form')->with('contact', $contact);
    }

    /**
     * عرض نموذج إعادة تعيين كلمة المرور
     */
    public function showResetForm()
    {
        return view('auth.reset-password');
    }

    /**
     * إعادة تعيين كلمة المرور
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'contact' => 'required|string',
            'reset_code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $contact = $validated['contact'];
        $resetCode = $validated['reset_code'];
        
        // تحديد نوع الاتصال
        $contactMethod = filter_var($contact, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        // البحث عن الحساب
        $account = Account::where($contactMethod, $contact)->first();
        
        if (!$account) {
            return back()->withErrors(['contact' => 'حساب غير موجود.']);
        }

        // التحقق من رمز إعادة التعيين
        $resetRecord = DB::table('password_resets')
            ->where('account_id', $account->account_id)
            ->where('reset_code', $resetCode)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['reset_code' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.']);
        }

        // تحديث كلمة المرور
        $account->password_hash = bcrypt($validated['password']);
        $account->save();

        // تحديث حالة الرمز إلى مستخدم
        DB::table('password_resets')
            ->where('reset_id', $resetRecord->reset_id)
            ->update([
                'is_used' => true,
                'used_at' => now(),
            ]);

        return redirect()->route('login')->with('success', 'تم تغيير كلمة المرور بنجاح. يمكنك الآن تسجيل الدخول.');
    }
}
