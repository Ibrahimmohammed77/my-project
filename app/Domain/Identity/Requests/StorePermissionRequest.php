<?php

namespace App\Domain\Identity\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100|unique:permissions,name',
            'resource_type' => 'required|string|max:100',
            'action' => 'required|string|max:50',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الصلاحية مطلوب',
            'name.unique' => 'اسم الصلاحية مستخدم مسبقاً',
            'name.max' => 'اسم الصلاحية يجب ألا يزيد عن 100 حرف',
            'resource_type.required' => 'نوع المورد مطلوب',
            'resource_type.max' => 'نوع المورد يجب ألا يزيد عن 100 حرف',
            'action.required' => 'الإجراء مطلوب',
            'action.max' => 'الإجراء يجب ألا يزيد عن 50 حرف',
            'description.max' => 'الوصف يجب ألا يزيد عن 500 حرف',
        ];
    }
}
