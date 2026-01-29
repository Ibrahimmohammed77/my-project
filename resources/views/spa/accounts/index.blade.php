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
    <x-modal id="account-modal" title="إضافة حساب جديد" maxWidth="5xl">
        <form id="account-form" class="space-y-4">
            <input type="hidden" id="account-id">
            
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-2">
                <x-form.input name="username" label="اسم المستخدم" icon="fa-user" placeholder="اسم المستخدم" required />
                <x-form.input name="full_name" label="الاسم الكامل" icon="fa-id-card" placeholder="الاسم الكامل" required />
                
                <x-form.input name="email" type="email" label="البريد الإلكتروني" icon="fa-envelope" placeholder="example@domain.com" required />
                <x-form.input name="phone" label="رقم الهاتف" icon="fa-phone" placeholder="05XXXXXXXX" />

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الحساب <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-briefcase absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <select id="account_type_id" onchange="handleAccountTypeChange(this)" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50" required>
                            <option value="" data-code="">اختر النوع...</option>
                            @foreach($types as $type)
                            <option value="{{ $type->lookup_value_id }}" data-code="{{ $type->code }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">حالة الحساب <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <select id="account_status_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50" required>
                            <option value="1">نشط</option>
                            <!-- Additional statuses can be loaded dynamically if needed -->
                        </select>
                        <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>

            <div id="password-field" class="border-t border-gray-100 pt-6 mt-2 p-2">
                <h3 class="text-sm font-bold text-gray-700 mb-2">كلمة المرور</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="password" type="password" label="كلمة المرور" icon="fa-lock" placeholder="******" />
                    <!-- Password confirmation removed as requested -->
                </div>
            </div>

            <!-- Conditional Sections based on Account Type -->
            
            <!-- Conditional Sections based on Account Type -->
            
            <!-- Studio Fields -->
            <div id="studio-fields" class="hidden border-t pt-4 mt-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-1 h-5 bg-purple-500 rounded-full"></span>
                    <h3 class="text-sm font-bold text-gray-700">بيانات الاستوديو</h3>
                </div>
                <!-- Studio has no extra fields now assuming name handled by main form -->
                <p class="text-sm text-gray-500">لا توجد بيانات إضافية للاستوديو.</p>
            </div>

            <!-- School Fields -->
            <div id="school-fields" class="hidden border-t pt-4 mt-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                    <h3 class="text-sm font-bold text-gray-700">بيانات المدرسة</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نوع المدرسة</label>
                        <select id="school_type_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm">
                            <option value="">اختر النوع (اختياري)...</option>
                            @foreach($schoolTypes as $type)
                            <option value="{{ $type->lookup_value_id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <p id="school_type_id-error" class="text-red-500 text-xs mt-1 hidden field-error"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">المرحلة الدراسية</label>
                        <select id="school_level_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm">
                            <option value="">اختر المرحلة (اختياري)...</option>
                            @foreach($schoolLevels as $level)
                            <option value="{{ $level->lookup_value_id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                        <p id="school_level_id-error" class="text-red-500 text-xs mt-1 hidden field-error"></p>
                    </div>
                </div>
            </div>

            <!-- Subscriber Fields -->
            <div id="subscriber-fields" class="hidden border-t pt-4 mt-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-1 h-5 bg-green-500 rounded-full"></span>
                    <h3 class="text-sm font-bold text-gray-700">بيانات المشترك</h3>
                </div>
                <p class="text-sm text-gray-500">لا توجد بيانات إضافية للمشترك.</p>
            </div>

        </form>

        <x-slot name="footer">
            <x-button type="submit" form="account-form" variant="primary">حفظ التغييرات</x-button>
            <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
        </x-slot>
    </x-modal>

    <script>
        window.accountConfig = {
            types: @json($accountTypeIds)
        };
    </script>
    
@push('scripts')
    @vite('resources/js/spa/pages/accounts.js')
@endpush
@endsection
