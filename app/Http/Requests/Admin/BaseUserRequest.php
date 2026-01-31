<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

abstract class BaseUserRequest extends FormRequest
{
    abstract public function authorize(): bool;

    public function rules(): array
    {
        $rules = [
            'username' => $this->usernameRules(),
            'full_name' => ['required', 'string', 'max:255'],
            'email' => $this->emailRules(),
            'phone' => ['nullable', 'string', 'yemeni_phone', $this->phoneUniqueRule()],
            'password' => $this->passwordRules(),
            'role_id' => ['required', 'exists:roles,role_id,is_active,1'],
            'user_status_id' => ['required', 'exists:lookup_values,lookup_value_id,is_active,1'],
            'is_active' => ['boolean'],
            'school_type_id' => ['nullable', 'exists:lookup_values,lookup_value_id,is_active,1'],
            'school_level_id' => ['nullable', 'exists:lookup_values,lookup_value_id,is_active,1'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ];

        return array_merge($rules, $this->additionalRules());
    }

    protected function usernameRules(): array
    {
        return [
            'required',
            'string',
            'username_format',
            'max:255',
            Rule::unique('users', 'username')->ignore($this->route('user')),
        ];
    }

    protected function emailRules(): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($this->route('user')),
        ];
    }

    protected function phoneUniqueRule(): Unique
{
    return Rule::unique('users', 'phone')
        ->ignore($this->route('user'));
}


    abstract protected function passwordRules(): array;

    protected function additionalRules(): array
    {
        return [];
    }

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
