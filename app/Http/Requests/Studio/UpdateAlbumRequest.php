<?php

namespace App\Http\Requests\Studio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateAlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('is-studio-owner');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'storage_library_id' => 'required|exists:storage_libraries,storage_library_id',
            'is_visible' => 'boolean',
            'card_ids' => 'nullable|array',
            'card_ids.*' => 'exists:cards,card_id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'اسم الألبوم',
            'description' => 'الوصف',
            'storage_library_id' => 'مكتبة التخزين',
            'is_visible' => 'حالة الظهور',
            'card_ids' => 'الكروت المرتبطة',
        ];
    }
}
