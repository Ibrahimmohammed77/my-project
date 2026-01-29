<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'storage_limit' => 'required|integer|min:0',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_albums' => 'required|integer|min:0',
            'max_cards' => 'required|integer|min:0',
            'max_users' => 'required|integer|min:0',
            'max_storage_libraries' => 'required|integer|min:0',
            'features' => 'required|array',
            'billing_cycle_id' => 'required|exists:lookup_values,lookup_value_id',
            'is_active' => 'boolean',
        ];
    }
}
