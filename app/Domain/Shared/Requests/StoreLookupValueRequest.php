<?php

namespace App\Domain\Shared\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLookupValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Define validation rules for creating LookupValue
        ];
    }
}
