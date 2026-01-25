<?php

namespace App\Domain\Access\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Define validation rules for creating CardGroup
        ];
    }
}
