<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $user = Auth::user();
        return [
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('كلمة السر الحالية غير صحيحة.');
                }
            }],
            'password' => ['required', 'confirmed', 'min:8', 'different:current_password'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'كلمة السر الحالية مطلوبة.',
            'password.required' => 'كلمة السر الجديدة مطلوبة.',
            'password.confirmed' => 'تأكيد كلمة السر غير متطابق.',
            'password.min' => 'كلمة السر الجديدة يجب أن لا تقل عن 8 أحرف.',
            'password.different' => 'كلمة السر الجديدة يجب أن تكون مختلفة عن الحالية.',
        ];
    }
}
