<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware/controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user') ? $this->route('user')->id : null;

        return [
            'username' => [
                'required',
                'string',
                'max:255',
                'username_format',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => ['nullable', 'string', 'yemeni_phone', Rule::unique('users', 'phone')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'strong_password'],
            'role_id' => ['required', 'exists:roles,role_id,is_active,1'],
            'user_status_id' => ['required', 'exists:lookup_values,lookup_value_id,is_active,1'],
            'is_active' => ['boolean'],
            'school_type_id' => ['nullable', 'exists:lookup_values,lookup_value_id,is_active,1'],
            'school_level_id' => ['nullable', 'exists:lookup_values,lookup_value_id,is_active,1'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'username' => 'اسم المستخدم',
            'full_name' => 'الاسم الكامل',
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الهاتف',
            'password' => 'كلمة المرور',
            'role_id' => 'الدور',
            'user_status_id' => 'حالة المستخدم',
            'is_active' => 'حالة التفعيل',
        ];
    }
}
