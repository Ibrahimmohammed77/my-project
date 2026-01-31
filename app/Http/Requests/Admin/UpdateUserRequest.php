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
        return ['nullable', 'string', 'min:8', 'strong_password'];
    }
}
