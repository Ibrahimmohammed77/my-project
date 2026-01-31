@extends('layouts.app')
@section('title', 'إدارة المدارس')
@section('header', 'إدارة المدارس')

@section('content')
    <x-page-header title="إدارة المدارس">
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
                @foreach($statuses as $status)
                <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                @endforeach
            </select>
            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        <!-- Create Button -->
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>مدرسة جديدة</span>
        </x-button>
    </x-page-header>

    <!-- Generic Table Component -->
    <x-table :headers="[
        ['name' => 'المدرسة', 'class' => 'w-1/4'],
        ['name' => 'معلومات الاتصال'],
        ['name' => 'النوع / المستوى'],
        ['name' => 'الحالة'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="schools">
        <!-- JS renders rows here -->
    </x-table>

    <!-- Create/Edit Modal Component -->
    <x-modal id="school-modal" title="إضافة مدرسة جديدة" maxWidth="5xl">
        <form id="school-form" class="space-y-4">
            <input type="hidden" id="school-id">

            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-2">
                <x-form.input name="name" label="اسم المدرسة" required icon="fa-school" placeholder="اسم المدرسة" />
                <x-form.input name="email" label="البريد الإلكتروني" type="email" icon="fa-envelope" placeholder="example@school.edu" />
                <x-form.input name="phone" label="رقم الهاتف" icon="fa-phone" placeholder="05xxxxxxxx" />
            </div>

            <!-- بيانات المدرسة -->
            <div class="border-t pt-4 mt-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                    <h3 class="text-sm font-bold text-gray-700">بيانات المدرسة</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع المدرسة <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-school absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="school_type_id" name="school_type_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                                @foreach($types as $type)
                                <option value="{{ $type->lookup_value_id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المرحلة الدراسية <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-graduation-cap absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="school_level_id" name="school_level_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                                @foreach($levels as $level)
                                <option value="{{ $level->lookup_value_id }}">{{ $level->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <x-form.input name="city" label="المدينة" icon="fa-map-marker-alt" placeholder="المدينة" />
                    <x-form.input name="address" label="العنوان" icon="fa-home" placeholder="العنوان بالتفصيل" />
                </div>
            </div>

            <!-- Credentials / Status -->
            <div id="credentials-section" class="border-t border-gray-100 pt-6 mt-2 p-2">
                <h3 class="text-sm font-bold text-gray-700 mb-2">بيانات الدخول والحالة</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="username" label="اسم المستخدم" icon="fa-user" placeholder="اتركه فارغاً للتوليد التلقائي" />
                    <x-form.input name="password" label="كلمة المرور" type="password" icon="fa-lock" placeholder="اتركها فارغة لاستخدام الافتراضي" />
                </div>
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">حالة المدرسة <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <select id="school_status_id" name="school_status_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                            @foreach($statuses as $status)
                            <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-button type="submit" form="school-form" variant="primary">حفظ التغييرات</x-button>
            <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
        </x-slot>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/modules/schools/index.js')
@endpush
@endsection
