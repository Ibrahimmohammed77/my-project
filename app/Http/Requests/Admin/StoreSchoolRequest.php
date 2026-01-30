<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|yemeni_phone|unique:users,phone',
            'city' => 'nullable|string|max:100',
            'school_type_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
            'school_level_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
            'school_status_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
            'username' => 'nullable|string|username_format|max:255|unique:users,username',
            'password' => 'nullable|string|min:8|strong_password',
            'address' => 'nullable|string',
        ];
    }
}
