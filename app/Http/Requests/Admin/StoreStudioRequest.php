<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudioRequest extends FormRequest
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
            'studio_status_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
            'username' => 'nullable|string|username_format|max:255|unique:users,username',
            'password' => 'nullable|string|min:8|strong_password',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ];
    }
}
