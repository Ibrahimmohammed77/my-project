<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Domain\Identity\Models\Account;
use App\Domain\Shared\Models\LookupValue;

class WebAuthController extends Controller
{
    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try to find account by username, email, or phone
        $account = Account::where('username', $request->login)
            ->orWhere('email', $request->login)
            ->orWhere('phone', $request->login)
            ->first();

        if (!$account) {
            throw ValidationException::withMessages([
                'login' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        // Verify password
        if (!Hash::check($request->password, $account->password_hash)) {
            throw ValidationException::withMessages([
                'login' => ['بيانات الدخول غير صحيحة.'],
            ]);
        }

        // Check account status
        if ($account->status && $account->status->code !== 'ACTIVE') {
            throw ValidationException::withMessages([
                'login' => ['الحساب غير نشط. يرجى التواصل مع الإدارة.'],
            ]);
        }

        // Login the user
        Auth::login($account, $request->filled('remember'));
        $request->session()->regenerate();
        
        // Update last login
        $account->last_login = now();
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'redirect' => route('dashboard')
        ]);
    }

    /**
     * Handle registration request
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

        // Get ACTIVE status (or PENDING if you want manual approval)
        $activeStatus = LookupValue::whereHas('master', function($q) {
            $q->where('code', 'ACCOUNT_STATUS');
        })->where('code', 'ACTIVE')->first();

        // Create the account
        $account = Account::create([
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'],
            'full_name' => $validated['full_name'],
            'password_hash' => Hash::make($validated['password']),
            'account_status_id' => $activeStatus?->lookup_value_id,
            'email_verified' => false,
            'phone_verified' => false,
        ]);

        // Auto-login the user
        Auth::login($account);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح',
            'redirect' => route('dashboard')
        ], 201);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
