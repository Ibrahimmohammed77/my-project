<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        if ($this->route('group')) {
            $this->merge([
                'card_group_id' => $this->route('group')->group_id
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'card_group_id' => 'required|exists:card_groups,group_id',
            'card_number' => 'nullable|string|unique:cards,card_number,' . $this->route('card')?->card_id . ',card_id',
            'card_type_id' => 'required|exists:lookup_values,lookup_value_id',
            'card_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'activation_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:activation_date',
            'notes' => 'nullable|string',
        ];
    }
}
