<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class LinkSchoolAlbumsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $card = $this->user()->school->cards()->find($this->route('card'));
        return $card !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $school = $this->user()->school;
        
        return [
            'album_ids' => 'required|array',
            'album_ids.*' => [
                'exists:albums,album_id',
                function ($attribute, $value, $fail) use ($school) {
                    if (!$school->albums()->where('album_id', $value)->exists()) {
                        $fail('الألبوم المختار غير تابع لمدرستك');
                    }
                },
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'album_ids' => 'الألبومات',
            'album_ids.*' => 'الألبوم المختار',
        ];
    }
}
