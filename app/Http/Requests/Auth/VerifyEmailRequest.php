<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:6']
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'كود التحقق مطلوب.',
            'code.string' => 'كود التحقق يجب أن يكون نصاً.',
            'code.size' => 'كود التحقق يجب أن يتكون من 6 أرقام.',
        ];
    }
}
