<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use App\Models\LookupValue;
use App\Models\ActivityLog; // إضافة هذا الاستيراد
use App\Services\AuthServiceInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthService implements AuthServiceInterface
{
    protected $userRepository;
    protected $notificationService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        NotificationService $notificationService
    ) {
        $this->userRepository = $userRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * Register a new user as customer.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = $this->userRepository->create($data);

            // Fire registered event
            event(new Registered($user));

            // Send welcome notification
            $this->sendWelcomeNotification($user);

            // Send verification code
            $this->sendVerificationNotification($user, 'email');

            // Log activity - استبدل activity() بـ ActivityLog::create()
            ActivityLog::log(
                $user->id,
                'user_registered',
                'user',
                $user->id,
                ['email' => $user->email]
            );

            return $user;
        });
    }

    public function login(string $login, string $password, bool $remember = false): array
    {
        $user = $this->findUserByLogin($login);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception('بيانات الاعتماد غير صحيحة.');
        }

        if (!$user->is_active) {
            throw new \Exception('حسابك غير نشط. يرجى التواصل مع الدعم.');
        }

        // Update last login
        $user->recordLogin();

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log activity - استبدل activity() بـ ActivityLog::create()
        ActivityLog::log(
            $user->id,
            'user_logged_in',
            'user',
            $user->id,
            ['ip' => request()->ip()]
        );

        return [
            'user' => $user->load(['status', 'type', 'roles']),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration', 525600),
        ];
    }

    /**
     * Find user by any of the login identifiers.
     */
    public function findUserByLogin(string $login): ?\App\Models\User
    {
        return $this->userRepository->findByLogin($login);
    }

    /**
     * Logout current user.
     */
    public function logout(): void
    {
        $user = Auth::user();

        if ($user) {
            // Revoke current token
            if ($user->currentAccessToken()) {
                $user->currentAccessToken()->delete();
            }

            // Log activity - استبدل activity() بـ ActivityLog::create()
            ActivityLog::log(
                $user->id,
                'user_logged_out',
                'user',
                $user->id
            );
        }

        Auth::logout();
    }

    /**
     * Get current authenticated user.
     */
    public function getCurrentUser(): ?User
    {
        $user = Auth::user();

        if ($user) {
            return $user->load([
                'status:id,code,name',
                'type:id,code,name',
                'roles:id,name',
                'customer',
                'storageAccount',
                'activeSubscription',
            ]);
        }

        return null;
    }

    /**
     * Refresh authentication token.
     */
    public function refreshToken(): string
    {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception('المستخدم غير مصادق عليه.');
        }

        // Revoke current token
        $user->currentAccessToken()->delete();

        // Create new token
        return $user->createToken('auth_token')->plainTextToken;
    }

    /**
     * Verify email with code.
     */
    public function verifyEmail(string $code): bool
    {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception('المستخدم غير مصادق عليه.');
        }

        if ($user->email_verified) {
            throw new \Exception('البريد الإلكتروني مفعل بالفعل.');
        }

        if ($user->verification_code !== $code) {
            throw new \Exception('كود التحقق غير صحيح.');
        }

        if (now()->gt($user->verification_expiry)) {
            throw new \Exception('كود التحقق منتهي الصلاحية.');
        }

        $updated = $user->update([
            'email_verified' => true,
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_expiry' => null,
        ]);

        if ($updated) {
            // Send verification success notification
            $this->notificationService->send(
                $user->id,
                'تم تفعيل البريد الإلكتروني',
                'تم تفعيل بريدك الإلكتروني بنجاح.',
                'email_verified'
            );

            // Log activity - استبدل activity() بـ ActivityLog::create()
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'email_verified',
                'resource_type' => 'user',
                'resource_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return $updated;
    }

    /**
     * Verify phone with code.
     */
    public function verifyPhone(string $code): bool
    {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception('المستخدم غير مصادق عليه.');
        }

        if ($user->phone_verified) {
            throw new \Exception('رقم الهاتف مفعل بالفعل.');
        }

        if ($user->verification_code !== $code) {
            throw new \Exception('كود التحقق غير صحيح.');
        }

        if (now()->gt($user->verification_expiry)) {
            throw new \Exception('كود التحقق منتهي الصلاحية.');
        }

        $updated = $user->update([
            'phone_verified' => true,
            'verification_code' => null,
            'verification_expiry' => null,
        ]);

        if ($updated) {
            // Send verification success notification
            $this->notificationService->send(
                $user->id,
                'تم تفعيل رقم الهاتف',
                'تم تفعيل رقم هاتفك بنجاح.',
                'phone_verified'
            );

            // Log activity - استبدل activity() بـ ActivityLog::create()
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'phone_verified',
                'resource_type' => 'user',
                'resource_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return $updated;
    }

    /**
     * Resend verification code.
     */
    public function resendVerificationCode(string $type): void
    {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception('المستخدم غير مصادق عليه.');
        }

        if ($type === 'email' && $user->email_verified) {
            throw new \Exception('البريد الإلكتروني مفعل بالفعل.');
        }

        if ($type === 'phone' && $user->phone_verified) {
            throw new \Exception('رقم الهاتف مفعل بالفعل.');
        }

        // Generate new code
        $code = rand(100000, 999999);
        $expiry = now()->addHours(24);

        $user->update([
            'verification_code' => $code,
            'verification_expiry' => $expiry,
        ]);

        // Send new code
        $this->sendVerificationNotification($user, $type);
    }

    /**
     * Send password reset link.
     */
    public function sendPasswordResetLink(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception(__($status));
        }
    }

    /**
     * Reset password.
     */
    public function resetPassword(array $data): bool
    {
        $status = Password::reset(
            $data,
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));

                // Log activity - استبدل activity() بـ ActivityLog::create()
                ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'password_reset',
                    'resource_type' => 'user',
                    'resource_id' => $user->id,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new \Exception(__($status));
        }

        return true;
    }

    /**
     * Send welcome notification.
     */
    private function sendWelcomeNotification(User $user): void
    {
        $this->notificationService->send(
            $user->id,
            'مرحباً بك في منصتنا',
            'شكراً لتسجيلك في منصتنا. نتمنى لك تجربة ممتعة.',
            'welcome'
        );
    }

    /**
     * Send verification notification.
     */
    private function sendVerificationNotification(User $user, string $type): void
    {
        $verificationType = LookupValue::where('code', $type . '_verification')->first();

        if ($verificationType && $user->verification_code) {
            $message = $type === 'email'
                ? "كود التحقق الخاص بك هو: {$user->verification_code}"
                : "كود التحقق الخاص بك هو: {$user->verification_code}";

            $this->notificationService->send(
                $user->id,
                'كود التحقق',
                $message,
                $verificationType->code,
                ['code' => $user->verification_code]
            );
        }
    }
}
