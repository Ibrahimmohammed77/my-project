<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // السماح للجميع بالتسجيل
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
                'regex:/^[a-zA-Z0-9_]+$/',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:180',
                'unique:users,email',
            ],
            'phone' => [
                'required',
                'string',
                'max:13',
                'unique:users,phone',
                'regex:/^(?:\+967|00967|967|0)?7\d{8}$/',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->numbers(),
            ],
            'password_confirmation' => ['required', 'string'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender_id' => ['nullable', 'exists:lookup_values,lookup_value_id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'username.regex' => 'اسم المستخدم يجب أن يحتوي على حروف إنجليزية وأرقام وشرطة سفلية فقط.',
            'phone.regex' => 'رقم الهاتف يجب أن يكون برقم يمني صحيح.',
            'password.confirmed' => 'كلمات السر غير متطابقة.',
            'gender_id.exists' => 'الجنس المحدد غير صحيح.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // تنظيف وتنسيق البيانات
        $this->merge([
            'phone' => $this->formatPhoneNumber($this->phone ?? ''),
            'email' => strtolower(trim($this->email ?? '')),
            'username' => strtolower(trim($this->username ?? '')),
        ]);
    }

    /**
     * Format phone number to standard format.
     */
    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Convert to Yemen format (967)
        if (str_starts_with($phone, '00967')) {
            return '+' . substr($phone, 2);
        } elseif (str_starts_with($phone, '967')) {
            return '+' . $phone;
        } elseif (str_starts_with($phone, '0')) {
             return '+967' . substr($phone, 1);
        } elseif (preg_match('/^7\d{8}$/', $phone)) {
             return '+967' . $phone;
        }

        return $phone;
    }
}
