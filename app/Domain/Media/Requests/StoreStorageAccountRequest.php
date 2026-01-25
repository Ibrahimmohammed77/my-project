<?php

namespace App\Domain\Media\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStorageAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Define validation rules for creating StorageAccount
        ];
    }
}
