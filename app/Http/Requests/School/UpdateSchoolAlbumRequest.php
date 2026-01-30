<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolAlbumRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasRole('school-owner');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
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
            'is_visible' => 'حالة الظهور',
            'card_ids' => 'الكروت المرتبطة',
        ];
    }
}
