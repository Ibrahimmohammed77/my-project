<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('school') ? $this->route('school')->user_id : null;

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => [
                'nullable',
                'string',
                'yemeni_phone',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'city' => 'nullable|string|max:100',
            'school_type_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
            'school_level_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
            'school_status_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
            'username' => [
                'nullable',
                'string',
                'username_format',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'password' => 'nullable|string|min:8|strong_password',
            'address' => 'nullable|string',
        ];
    }
}
