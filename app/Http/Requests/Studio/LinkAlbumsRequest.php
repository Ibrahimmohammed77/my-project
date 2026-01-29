<?php

namespace App\Http\Requests\Studio;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class LinkAlbumsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $card = $this->route('card');
        $studio = $this->user()->studio;

        if (!$studio || !$card) {
            return false;
        }

        return (int)$card->owner_id === (int)$studio->studio_id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $studio = auth()->user()->studio;
        
        return [
            'album_ids' => 'required|array',
            'album_ids.*' => [
                'exists:albums,album_id',
                function ($attribute, $value, $fail) use ($studio) {
                    if (!$studio->albums()->where('album_id', $value)->exists()) {
                        $fail('الألبوم المختار غير تابع للاستوديو الخاص بك');
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
