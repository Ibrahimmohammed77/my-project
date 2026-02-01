<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());

            // Generate token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'تم التسجيل بنجاح',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => config('sanctum.expiration', 525600),
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء التسجيل',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            
            $result = $this->authService->login(
                $data['login'],
                $data['password'],
                $data['remember'] ?? false
            );

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الدخول بنجاح',
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }

  
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الخروج بنجاح'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء تسجيل الخروج'
            ], 500);
        }
    }

 
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'المستخدم غير مصادق عليه'
                ], 401);
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $user
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Get current user error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'حدث خطأ أثناء جلب بيانات المستخدم'
            ], 500);
        }
    }

    
    public function refresh(Request $request): JsonResponse
    {
        try {
            $token = $this->authService->refreshToken();

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث التوكن بنجاح',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => config('sanctum.expiration', 525600),
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Refresh token error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 401);
        }
    }


    public function verifyEmail(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'code' => ['required', 'string', 'size:6']
            ]);

            $success = $this->authService->verifyEmail($validated['code']);

            if ($success) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'تم تفعيل البريد الإلكتروني بنجاح'
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'فشل في تفعيل البريد الإلكتروني'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Verify email error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    
    public function resendVerification(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => ['required', 'string', 'in:email,phone']
            ]);

            $this->authService->resendVerificationCode($validated['type']);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال كود التحقق بنجاح'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Resend verification error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email']
            ]);

            $this->authService->sendPasswordResetLink($validated['email']);

            return response()->json([
                'status' => 'success',
                'message' => 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Forgot password error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'token' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['required', 'confirmed', 'min:8'],
            ]);

            $success = $this->authService->resetPassword($validated);

            if ($success) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'تم إعادة تعيين كلمة المرور بنجاح'
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'فشل في إعادة تعيين كلمة المرور'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Reset password error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}