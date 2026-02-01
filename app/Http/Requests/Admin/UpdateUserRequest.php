<?php

namespace App\Http\Requests\Admin;

class UpdateUserRequest extends BaseUserRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function passwordRules(): array
    {
        return ['nullable', 'string', 'min:4'];
    }

    public function messages(): array
    {
        return [
            'password.min' => 'كلمة السر يجب أن لا تقل عن 4 أحرف.',
        ];
    }
}
