<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

abstract class BasePlanRequest extends FormRequest
{
    abstract public function authorize(): bool;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'storage_limit' => 'required|integer|min:0',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'required|array',
            'is_active' => 'boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'اسم الخطة',
            'description' => 'الوصف',
            'storage_limit' => 'حد التخزين',
            'price_monthly' => 'السعر الشهري',
            'price_yearly' => 'السعر السنوي',
            'features' => 'الميزات',
            'is_active' => 'حالة التفعيل',
        ];
    }
}
