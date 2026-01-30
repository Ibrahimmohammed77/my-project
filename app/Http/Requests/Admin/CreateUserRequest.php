<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('manage_users') || Gate::allows('access_admin_dashboard');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => 'required|string|username_format|max:255|unique:users,username',
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|yemeni_phone|unique:users,phone',
            'password' => 'required|string|min:8|strong_password',
            'role_id' => 'required|exists:roles,role_id,is_active,1',
            'user_status_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
            'is_active' => 'boolean',
            'school_type_id' => 'nullable|exists:lookup_values,lookup_value_id,is_active,1',
            'school_level_id' => 'nullable|exists:lookup_values,lookup_value_id,is_active,1',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'username.required' => 'اسم المستخدم مطلوب.',
            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 8 أحرف.',
            'role_id.required' => 'يجب تحديد دور للمستخدم.',
            'role_id.exists' => 'الدور المحدد غير موجود.',
        ];
    }
}
