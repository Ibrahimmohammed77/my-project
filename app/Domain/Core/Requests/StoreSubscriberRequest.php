<?php

namespace App\Domain\Core\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,account_id',
            'subscriber_status_id' => 'required|exists:lookup_values,lookup_value_id',
            'settings' => 'nullable|array'
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.required' => 'الحساب مطلوب',
            'account_id.exists' => 'الحساب المحدد غير موجود',
            'subscriber_status_id.required' => 'حالة المشترك مطلوبة',
            'subscriber_status_id.exists' => 'حالة المشترك المحددة غير موجودة',
            'settings.array' => 'الإعدادات يجب أن تكون مصفوفة',
        ];
    }
}
