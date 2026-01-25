<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Domain\Identity\Models\Account;
use App\Domain\Shared\Models\LookupValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * تسجيل الدخول
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // يمكن أن يكون username أو email أو phone
            'password' => 'required|string',
        ]);

        // البحث عن الحساب
        $account = Account::where('username', $request->login)
            ->orWhere('email', $request->login)
            ->orWhere('phone', $request->login)
            ->first();

        if (!$account || !Hash::check($request->password, $account->password_hash)) {
            throw ValidationException::withMessages([
                'login' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        // التحقق من حالة الحساب
        if ($account->status && $account->status->code !== 'ACTIVE') {
            throw ValidationException::withMessages([
                'login' => ['الحساب غير نشط. يرجى التواصل مع الإدارة.'],
            ]);
        }

        // إنشاء token
        $token = $account->createToken('auth-token')->plainTextToken;

        // تحديث آخر تسجيل دخول
        $account->last_login = now();
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'account' => [
                    'account_id' => $account->account_id,
                    'username' => $account->username,
                    'email' => $account->email,
                    'full_name' => $account->full_name,
                    'phone' => $account->phone,
                    'profile_image' => $account->profile_image,
                ],
                'token' => $token,
            ]
        ]);
    }

    /**
     * تسجيل حساب جديد
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:accounts,username',
            'email' => 'nullable|email|max:100|unique:accounts,email',
            'phone' => 'required|string|max:20|unique:accounts,phone',
            'full_name' => 'required|string|max:100',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // الحصول على حالة PENDING
        $pendingStatus = LookupValue::whereHas('master', function($q) {
            $q->where('code', 'ACCOUNT_STATUS');
        })->where('code', 'PENDING')->first();

        // إنشاء الحساب
        $account = Account::create([
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'full_name' => $validated['full_name'],
            'password_hash' => Hash::make($validated['password']),
            'account_status_id' => $pendingStatus?->lookup_value_id,
            'email_verified' => false,
            'phone_verified' => false,
        ]);

        // إنشاء token
        $token = $account->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'data' => [
                'account' => [
                    'account_id' => $account->account_id,
                    'username' => $account->username,
                    'email' => $account->email,
                    'full_name' => $account->full_name,
                ],
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    /**
     * معلومات المستخدم الحالي
     */
    public function me(Request $request)
    {
        $account = $request->user();
        $account->load(['status', 'roles.permissions']);

        return response()->json([
            'success' => true,
            'data' => [
                'account' => [
                    'account_id' => $account->account_id,
                    'username' => $account->username,
                    'email' => $account->email,
                    'full_name' => $account->full_name,
                    'phone' => $account->phone,
                    'profile_image' => $account->profile_image,
                    'status' => $account->status,
                    'roles' => $account->roles,
                    'last_login' => $account->last_login,
                ]
            ]
        ]);
    }

    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        $account = $request->user();

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:100',
            'email' => 'sometimes|nullable|email|max:100|unique:accounts,email,' . $account->account_id . ',account_id',
            'phone' => 'sometimes|string|max:20|unique:accounts,phone,' . $account->account_id . ',account_id',
            'profile_image' => 'sometimes|nullable|string|max:255',
        ]);

        $account->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'data' => ['account' => $account]
        ]);
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $account = $request->user();

        if (!Hash::check($validated['current_password'], $account->password_hash)) {
            throw ValidationException::withMessages([
                'current_password' => ['كلمة المرور الحالية غير صحيحة.'],
            ]);
        }

        $account->password_hash = Hash::make($validated['new_password']);
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ]);
    }
}
