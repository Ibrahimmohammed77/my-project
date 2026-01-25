<div class="space-y-4">
    <x-form.input name="full_name" label="الاسم الكامل" :value="$account->full_name ?? ''" required />
    <x-form.input name="username" label="اسم المستخدم" :value="$account->username ?? ''" required />
    <x-form.input name="email" label="البريد الإلكتروني" type="email" :value="$account->email ?? ''" required />
    <x-form.input name="phone" label="رقم الهاتف" :value="$account->phone ?? ''" />

    <x-form.select 
        name="account_status_id" 
        label="الحالة" 
        :options="$statuses" 
        :selected="$account->account_status_id ?? ''" 
        required 
    />

    <x-form.input name="password" label="كلمة المرور" type="password" :required="!isset($account)" />
    @if(isset($account))
    <p class="text-xs text-gray-500 -mt-2">اترك كلمة المرور فارغة للاحتفاظ بكلمة المرور الحالية</p>
    @endif
</div>
