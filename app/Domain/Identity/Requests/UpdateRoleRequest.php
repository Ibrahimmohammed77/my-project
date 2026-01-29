<?php

namespace App\Domain\Identity\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role');
        
        return [
            'name' => 'required|string|max:100|unique:roles,name,' . $roleId . ',role_id',
            'description' => 'nullable|string|max:500',
            'is_system' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الدور مطلوب',
            'name.unique' => 'اسم الدور مستخدم مسبقاً',
            'name.max' => 'اسم الدور يجب ألا يزيد عن 100 حرف',
            'description.max' => 'الوصف يجب ألا يزيد عن 500 حرف',
            'is_system.boolean' => 'حقل النظام يجب أن يكون صحيح أو خطأ',
        ];
    }
}
