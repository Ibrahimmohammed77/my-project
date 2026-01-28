<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('customer');
        
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:customers,email,' . $id . ',customer_id',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender_id' => 'required|exists:lookup_values,lookup_value_id',
            'account_id' => 'sometimes|exists:accounts,account_id'
        ];
    }
}
