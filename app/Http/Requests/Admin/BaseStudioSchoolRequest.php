<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

abstract class BaseStudioSchoolRequest extends FormRequest
{
    /**
     * Get the entity type (studio or school).
     */
    abstract protected function entityType(): string;

    /**
     * Get the permission required for this request.
     */
    abstract protected function permission(): string;

    /**
     * Get the status field name.
     */
    abstract protected function statusField(): string;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows($this->permission());
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $userId = $this->getUserId();
        $isUpdate = !is_null($userId);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => $this->emailRules($userId),
            'phone' => $this->phoneRules($userId),
            'username' => $this->usernameRules($userId),
            'password' => $isUpdate ? 'nullable|string|min:8|strong_password' : 'required|string|min:8|strong_password',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        // Add status field
        $rules[$this->statusField()] = 'required|exists:lookup_values,lookup_value_id,is_active,1';

        // Add entity-specific rules
        $rules = array_merge($rules, $this->entitySpecificRules());

        return $rules;
    }

    /**
     * Get entity-specific validation rules.
     */
    protected function entitySpecificRules(): array
    {
        if ($this->entityType() === 'school') {
            return [
                'school_type_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
                'school_level_id' => 'required|exists:lookup_values,lookup_value_id,is_active,1',
            ];
        }

        return [];
    }

    /**
     * Get the user ID from the route parameter.
     */
    protected function getUserId(): ?int
    {
        $entity = $this->route($this->entityType());
        return $entity ? $entity->user_id : null;
    }

    /**
     * Get email validation rules.
     */
    protected function emailRules(?int $userId): array
    {
        $rules = [
            'required',
            'string',
            'email',
            'max:255',
            'lowercase',
        ];

        if ($userId) {
            $rules[] = Rule::unique('users', 'email')->ignore($userId);
        } else {
            $rules[] = 'unique:users,email';
        }

        return $rules;
    }

    /**
     * Get phone validation rules.
     */
    protected function phoneRules(?int $userId): array
    {
        $rules = [
            'nullable',
            'string',
            'yemeni_phone',
        ];

        if ($userId) {
            $rules[] = Rule::unique('users', 'phone')->ignore($userId);
        } else {
            $rules[] = 'unique:users,phone';
        }

        return $rules;
    }

    /**
     * Get username validation rules.
     */
    protected function usernameRules(?int $userId): array
    {
        $rules = [
            'nullable',
            'string',
            'username_format',
            'max:255',
            'lowercase',
        ];

        if ($userId) {
            $rules[] = Rule::unique('users', 'username')->ignore($userId);
        } else {
            $rules[] = 'unique:users,username';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $entityName = $this->entityType() === 'school' ? 'المدرسة' : 'الاستوديو';

        return [
            'name.required' => "اسم {$entityName} مطلوب.",
            'name.max' => "اسم {$entityName} يجب أن لا يتجاوز 255 حرفًا.",

            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'email.max' => 'البريد الإلكتروني يجب أن لا يتجاوز 255 حرفًا.',

            'phone.unique' => 'رقم الهاتف مستخدم بالفعل.',
            'phone.yemeni_phone' => 'رقم الهاتف اليمني غير صالح.',

            'username.unique' => 'اسم المستخدم مستخدم بالفعل.',
            'username.username_format' => 'صيغة اسم المستخدم غير صالحة.',
            'username.max' => 'اسم المستخدم يجب أن لا يتجاوز 255 حرفًا.',

            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن لا تقل عن 8 أحرف.',
            'password.strong_password' => 'كلمة المرور يجب أن تحتوي على حرف كبير، حرف صغير، رقم ورمز خاص.',

            'city.max' => 'المدينة يجب أن لا تتجاوز 100 حرف.',
            'address.max' => 'العنوان يجب أن لا يتجاوز 500 حرف.',
            'description.max' => 'الوصف يجب أن لا يتجاوز 1000 حرف.',

            'logo.image' => 'الملف يجب أن يكون صورة.',
            'logo.mimes' => 'نوع الصورة غير مدعوم. الرجاء استخدام: jpeg, png, jpg, gif, svg.',
            'logo.max' => 'حجم الصورة يجب أن لا يتجاوز 2 ميجابايت.',

            // Status field message
            "{$this->statusField()}.required" => 'حالة الحساب مطلوبة.',
            "{$this->statusField()}.exists" => 'حالة الحساب المحددة غير موجودة.',

            // School specific messages
            'school_type_id.required' => 'نوع المدرسة مطلوب.',
            'school_type_id.exists' => 'نوع المدرسة المحدد غير موجود.',
            'school_level_id.required' => 'مستوى المدرسة مطلوب.',
            'school_level_id.exists' => 'مستوى المدرسة المحدد غير موجود.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        $entityName = $this->entityType() === 'school' ? 'المدرسة' : 'الاستوديو';

        $attributes = [
            'name' => "اسم {$entityName}",
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الهاتف',
            'username' => 'اسم المستخدم',
            'password' => 'كلمة المرور',
            'city' => 'المدينة',
            'address' => 'العنوان',
            'description' => 'الوصف',
            'logo' => 'الشعار',
            $this->statusField() => 'حالة الحساب',
        ];

        if ($this->entityType() === 'school') {
            $attributes['school_type_id'] = 'نوع المدرسة';
            $attributes['school_level_id'] = 'مستوى المدرسة';
        }

        return $attributes;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $phone = $this->phone;
        if ($phone) {
            // Keep only digits and leading +
            $phone = preg_replace('/(?<!^)\+|[^\d+]/', '', $phone);
        }

        $this->merge([
            'email' => strtolower($this->email),
            'username' => $this->username ? strtolower($this->username) : null,
            'phone' => $phone,
        ]);
    }

    /**
     * Get validated data with additional processing.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Map status field to user_status_id
        $statusField = $this->statusField();
        if (isset($validated[$statusField])) {
            $validated['user_status_id'] = $validated[$statusField];
            unset($validated[$statusField]);
        }

        // Map name to full_name for user model
        if (isset($validated['name'])) {
            $validated['full_name'] = $validated['name'];
        }

        return $validated;
    }
}
