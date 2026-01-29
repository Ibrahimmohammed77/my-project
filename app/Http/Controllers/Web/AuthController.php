<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;

class AuthController extends Controller implements HasMiddleware
{
    protected $authService;

    public static function middleware(): array
    {
        return [
            new Middleware('guest', except: ['logout', 'profile', 'updateProfile', 'showChangePasswordForm', 'changePassword']),
        ];
    }

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * عرض صفحة تسجيل الدخول
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * عرض صفحة التسجيل
     */
    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    /**
     * تسجيل الدخول (للويب)
     */
    public function login(LoginRequest $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $data = $request->validated();
            $login = $request->input('login');

            // Determine login type
            $loginType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : (is_numeric($login) ? 'phone' : 'username');

            $credentials = [
                $loginType => $login,
                'password' => $request->input('password')
            ];

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $user = Auth::user();

                if (!$user->is_active) {
                    Auth::logout();
                    if ($request->wantsJson()) {
                        return response()->json(['message' => 'حسابك غير نشط. يرجى التواصل مع الدعم.'], 403);
                    }
                    throw new \Exception('حسابك غير نشط. يرجى التواصل مع الدعم.');
                }

                $user->recordLogin();

                // Create API token for web session if needed
                $token = $user->createToken('web_auth_token')->plainTextToken;
                session(['api_token' => $token]);

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'تم تسجيل الدخول بنجاح!',
                        'redirect' => route('dashboard'),
                        'user' => $user
                    ]);
                }

                return redirect()->intended(route('dashboard'))
                    ->with('success', 'تم تسجيل الدخول بنجاح!');
            }

            if ($request->wantsJson()) {
                return response()->json(['message' => 'بيانات الاعتماد غير صحيحة.'], 401);
            }

            throw new \Exception('بيانات الاعتماد غير صحيحة.');
        } catch (\Exception $e) {
            Log::error('Web login error: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()
                ->withInput($request->only('login', 'remember'))
                ->withErrors(['login' => $e->getMessage()]);
        }
    }

    /**
     * تسجيل مستخدم جديد (للويب)
     */
    public function register(RegisterRequest $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());

            Auth::login($user, $request->filled('remember'));

            $token = $user->createToken('web_auth_token')->plainTextToken;
            session(['api_token' => $token]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم إنشاء حسابك بنجاح! نتمنى لك تجربة ممتعة.',
                    'redirect' => route('dashboard'),
                    'user' => $user
                ]);
            }

            return redirect()->route('dashboard')
                ->with('success', 'تم إنشاء حسابك بنجاح! نتمنى لك تجربة ممتعة.');
        } catch (\Exception $e) {
            Log::error('Web registration error: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['register' => $e->getMessage()]);
        }
    }

    /**
     * تسجيل الخروج (للويب)
     */
    public function logout(Request $request): RedirectResponse
    {
        try {
            $user = Auth::user();

            if ($user) {
                // حذف جميع التوكنات (check if method exists and returns relation)
                if (method_exists($user, 'tokens') && $user->tokens()) {
                    $user->tokens()->delete();
                }

                // Log activity using AuthService
                $this->authService->logout();
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('success', 'تم تسجيل الخروج بنجاح.');
        } catch (\Throwable $e) {
            Log::error('Web logout error: ' . $e->getMessage());

            return back()->with('error', 'حدث خطأ أثناء تسجيل الخروج.');
        }
    }

    /**
     * عرض نموذج نسيان كلمة المرور
     */
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * إرسال رابط إعادة تعيين كلمة المرور
     */
    public function forgotPassword(ForgotPasswordRequest $request): RedirectResponse
    {
        try {
            $this->authService->sendPasswordResetLink($request->email);

            return back()
                ->with('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني. يرجى التحقق من بريدك.');
        } catch (\Exception $e) {
            Log::error('Forgot password error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['email' => $e->getMessage()]);
        }
    }

    /**
     * عرض نموذج إعادة تعيين كلمة المرور
     */
    public function showResetPasswordForm(Request $request): View
    {
        return view('auth.reset-password', [
            'token' => $request->route('token'),
            'email' => $request->email
        ]);
    }

    /**
     * إعادة تعيين كلمة المرور
     */
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        try {
            $success = $this->authService->resetPassword($request->validated());

            if ($success) {
                return redirect()->route('login')
                    ->with('success', 'تم إعادة تعيين كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.');
            }

            return back()
                ->withInput()
                ->with('error', 'فشل في إعادة تعيين كلمة المرور.');
        } catch (\Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['password' => $e->getMessage()]);
        }
    }

    /**
     * عرض صفحة التحقق من البريد الإلكتروني
     */
    public function showVerifyEmailForm(): View|RedirectResponse
    {
        if (Auth::user()->email_verified) {
            return redirect()->route('dashboard')
                ->with('info', 'بريدك الإلكتروني مفعل بالفعل.');
        }

        return view('auth.verify-email');
    }

    /**
     * التحقق من البريد الإلكتروني
     */
    public function verifyEmail(VerifyEmailRequest $request): RedirectResponse
    {
        try {
            $success = $this->authService->verifyEmail($request->code);

            if ($success) {
                return redirect()->route('dashboard')
                    ->with('success', 'تم تفعيل بريدك الإلكتروني بنجاح!');
            }

            return back()
                ->with('error', 'فشل في تفعيل البريد الإلكتروني.');
        } catch (\Exception $e) {
            Log::error('Verify email error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['code' => $e->getMessage()]);
        }
    }

    /**
     * إعادة إرسال كود التحقق
     */
    public function resendVerification(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'type' => ['required', 'string', 'in:email,phone']
            ]);

            $this->authService->resendVerificationCode($validated['type']);

            return back()
                ->with('success', 'تم إرسال كود التحقق بنجاح. يرجى التحقق من بريدك/هاتفك.');
        } catch (\Exception $e) {
            Log::error('Resend verification error: ' . $e->getMessage());

            return back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * عرض صفحة ملف المستخدم
     */
    public function profile(): View
    {
        $user = Auth::user();

        return view('auth.profile', compact('user'));
    }

    /**
     * تحديث ملف المستخدم
     */
    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();
            $user->update($request->validated());

            return back()
                ->with('success', 'تم تحديث الملف الشخصي بنجاح.');
        } catch (\Exception $e) {
            Log::error('Update profile error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الملف الشخصي.');
        }
    }

    /**
     * عرض نموذج تغيير كلمة المرور
     */
    public function showChangePasswordForm(): View
    {
        return view('auth.change-password');
    }

    /**
     * تغيير كلمة المرور
     */
    public function changePassword(ChangePasswordRequest $request): RedirectResponse
    {
        try {
            $user = Auth::user();

            $user->update([
                'password' => Hash::make($request->password)
            ]);

            return back()
                ->with('success', 'تم تغيير كلمة المرور بنجاح.');
        } catch (\Exception $e) {
            Log::error('Change password error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تغيير كلمة المرور.');
        }
    }
}
