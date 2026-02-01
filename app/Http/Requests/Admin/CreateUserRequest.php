<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Gate;

class CreateUserRequest extends BaseUserRequest
{
    public function authorize(): bool
    {
        return Gate::any(['manage_users', 'access_admin_dashboard']);
    }

    protected function passwordRules(): array
    {
        return ['required', 'string', 'min:4'];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.required' => 'كلمة السر مطلوبة.',
            'password.min' => 'كلمة السر يجب أن لا تقل عن 4 أحرف.',
            'role_id.required' => 'يجب تحديد دور للمستخدم.',
            'role_id.exists' => 'الدور المحدد غير موجود.',
        ];
    }
}
