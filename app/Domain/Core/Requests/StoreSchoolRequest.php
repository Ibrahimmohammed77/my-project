<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,account_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|string|max:500',
            'school_type_id' => 'required|exists:lookup_values,lookup_value_id',
            'school_level_id' => 'required|exists:lookup_values,lookup_value_id',
            'school_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'email' => 'nullable|email|max:255|unique:schools,email',
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
            'account_id.required' => 'الحساب مطلوب',
            'account_id.exists' => 'الحساب المحدد غير موجود',
            'name.required' => 'اسم المدرسة مطلوب',
            'name.max' => 'اسم المدرسة يجب ألا يزيد عن 255 حرف',
            'description.max' => 'الوصف يجب ألا يزيد عن 1000 حرف',
            'school_type_id.required' => 'نوع المدرسة مطلوب',
            'school_type_id.exists' => 'نوع المدرسة المحدد غير موجود',
            'school_level_id.required' => 'مستوى المدرسة مطلوب',
            'school_level_id.exists' => 'مستوى المدرسة المحدد غير موجود',
            'school_status_id.required' => 'حالة المدرسة مطلوبة',
            'school_status_id.exists' => 'حالة المدرسة المحددة غير موجودة',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            'phone.max' => 'رقم الهاتف يجب ألا يزيد عن 20 حرف',
            'website.url' => 'صيغة الموقع الإلكتروني غير صحيحة',
            'settings.array' => 'الإعدادات يجب أن تكون مصفوفة',
        ];
    }
}
