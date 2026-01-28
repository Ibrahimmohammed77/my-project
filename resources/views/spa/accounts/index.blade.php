@extends('layouts.app')
@section('title', 'إدارة الحسابات')
@section('header', 'إدارة الحسابات')

@section('content')
    <x-page-header title="إدارة الحسابات">
        <!-- Search -->
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>

        <!-- Filter -->
        <div class="relative min-w-[160px]">
            <i class="fas fa-filter absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <select id="status-filter" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer text-gray-600">
                <option value="">جميع الحالات</option>
                <option value="ACTIVE">نشط</option>
                <option value="PENDING">قيد المراجعة</option>
                <option value="SUSPENDED">موقوف</option>
            </select>
            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>
        
        <!-- Create Button -->
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>حساب جديد</span>
        </x-button>
    </x-page-header>

    <!-- Generic Table Component -->
    <x-table :headers="[
        ['name' => 'المستخدم', 'class' => 'w-1/4'],
        ['name' => 'معلومات الاتصال'],
        ['name' => 'الحالة'],
        ['name' => 'الأدوار'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="accounts">
        <!-- JS renders rows here -->
    </x-table>

    <!-- Create/Edit Modal Component -->
    <x-modal id="account-modal" title="إضافة حساب جديد">
        <form id="account-form" class="space-y-4">
            <input type="hidden" id="account-id">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form.input name="username" label="اسم المستخدم" required icon="fa-at" placeholder="username" />
                <x-form.input name="full_name" label="الاسم الكامل" required icon="fa-user" placeholder="الاسم الظاهر" />
            </div>

            <x-form.input name="email" label="البريد الإلكتروني" type="email" icon="fa-envelope" placeholder="example@domain.com" />
            
            <x-form.input name="phone" label="رقم الهاتف" required icon="fa-phone" placeholder="05xxxxxxxx" />

            <div id="password-field">
                <x-form.input name="password" label="كلمة المرور" type="password" required icon="fa-lock" placeholder="••••••••" />
            </div>

            <!-- Account Type and Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع الحساب <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-briefcase absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <select id="account_type_id" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer">
                            <option value="">اختر النوع...</option>
                            @foreach($types as $type)
                            <option value="{{ $type->lookup_value_id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">حالة الحساب <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <select id="account_status_id" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer">
                            <option value="1">نشط</option>
                            <option value="2">قيد المراجعة</option>
                            <option value="3">موقوف</option>
                        </select>
                        <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-button type="submit" form="account-form" variant="primary">حفظ التغييرات</x-button>
            <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
        </x-slot>
    </x-modal>

@push('scripts')
@push('scripts')
    @vite('resources/js/spa/pages/accounts.js')
@endpush
@endsection
@endpush
@endsection
