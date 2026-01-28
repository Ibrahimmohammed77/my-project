<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => 'sometimes|exists:accounts,account_id',
            'subscriber_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'settings' => 'nullable|array'
        ];
    }
}
