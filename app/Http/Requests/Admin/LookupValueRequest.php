<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LookupValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lookup_master_id' => 'required|exists:lookup_masters,lookup_master_id',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('lookup_values')
                    ->where('lookup_master_id', $this->input('lookup_master_id'))
                    ->ignore($this->route('value')?->lookup_value_id, 'lookup_value_id'),
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ];
    }
}
