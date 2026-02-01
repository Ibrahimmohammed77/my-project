<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'التوكن مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'يرجى إدخال بريد إلكتروني صحيح.',
            'password.required' => 'كلمة السر مطلوبة.',
            'password.confirmed' => 'تأكيد كلمة السر غير متطابق.',
            'password.min' => 'كلمة السر يجب أن لا تقل عن 8 أحرف.',
        ];
    }
}
