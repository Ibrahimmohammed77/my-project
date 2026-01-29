<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studioId = $this->route('studio');
        
        return [
            'account_id' => 'sometimes|exists:accounts,account_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|string|max:500',
            'studio_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'email' => 'nullable|email|max:255|unique:studios,email,' . $studioId . ',studio_id',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:500',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'settings' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.exists' => 'الحساب المحدد غير موجود',
            'name.required' => 'اسم الاستوديو مطلوب',
            'name.max' => 'اسم الاستوديو يجب ألا يزيد عن 255 حرف',
            'description.max' => 'الوصف يجب ألا يزيد عن 1000 حرف',
            'studio_status_id.required' => 'حالة الاستوديو مطلوبة',
            'studio_status_id.exists' => 'حالة الاستوديو المحددة غير موجودة',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            'phone.max' => 'رقم الهاتف يجب ألا يزيد عن 20 حرف',
            'website.url' => 'صيغة الموقع الإلكتروني غير صحيحة',
            'settings.array' => 'الإعدادات يجب أن تكون مصفوفة',
        ];
    }
}
