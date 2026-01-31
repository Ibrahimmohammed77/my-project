<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Gate;

class CreatePlanRequest extends BasePlanRequest
{
    public function authorize(): bool
    {
        return Gate::allows('manage_plans');
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الخطة مطلوب.',
            'description.required' => 'وصف الخطة مطلوب.',
            'storage_limit.required' => 'حد التخزين مطلوب.',
            'storage_limit.min' => 'حد التخزين يجب أن يكون قيمة موجبة.',
            'price_monthly.required' => 'السعر الشهري مطلوب.',
            'price_yearly.required' => 'السعر السنوي مطلوب.',
        ];
    }
}
