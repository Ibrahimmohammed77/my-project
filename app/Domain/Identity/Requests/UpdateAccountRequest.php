<?php

namespace App\Domain\Identity\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accountId = $this->route('account');
        
        return [
            'username' => 'required|string|max:100|unique:accounts,username,' . $accountId . ',account_id',
            'email' => 'required|email|max:255|unique:accounts,email,' . $accountId . ',account_id',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|string|max:500',
            'account_type_id' => 'sometimes|exists:lookup_values,lookup_value_id',
            'account_status_id' => 'sometimes|exists:lookup_values,lookup_value_id',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'اسم المستخدم مطلوب',
            'username.unique' => 'اسم المستخدم مستخدم مسبقاً',
            'username.max' => 'اسم المستخدم يجب ألا يزيد عن 100 حرف',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'البريد الإلكتروني مستخدم مسبقاً',
            'full_name.required' => 'الاسم الكامل مطلوب',
            'full_name.max' => 'الاسم الكامل يجب ألا يزيد عن 255 حرف',
            'phone.max' => 'رقم الهاتف يجب ألا يزيد عن 20 حرف',
            'account_type_id.exists' => 'نوع الحساب المحدد غير موجود',
            'account_status_id.exists' => 'حالة الحساب المحددة غير موجودة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ];
    }

    protected function prepareForValidation()
    {
        // Hash the password if provided
        if ($this->has('password') && $this->password) {
            $this->merge([
                'password_hash' => bcrypt($this->password)
            ]);
        }
    }
}
