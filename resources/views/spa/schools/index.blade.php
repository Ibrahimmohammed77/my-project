@extends('layouts.app')
@section('title', 'إدارة المدارس')
@section('header', 'إدارة المدارس')

@section('content')
    <x-page-header 
        title="إدارة المدارس" 
        subtitle="إدارة بيانات المدارس، تنظيم مجموعات الكروت وتوزيعها على الفروع والأقسام."
        :breadcrumbs="[['label' => 'إدارة الحسابات', 'url' => route('spa.accounts')], ['label' => 'المدارس']]"
    >
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

    <!-- School Details Modal -->
    <x-modal id="school-details-modal" title="تفاصيل المدرسة" maxWidth="4xl">
        <div id="school-details-content" class="p-6 space-y-8 text-right">
            <!-- Loading State -->
            <div id="school-details-loading" class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-circle-notch fa-spin text-4xl text-accent mb-4"></i>
                <p class="text-gray-500 font-bold">جاري تحميل البيانات...</p>
            </div>

            <!-- Details Content (Hidden by default) -->
            <div id="school-details-data" class="hidden space-y-8">
                <!-- Profile Header -->
                <div class="flex items-start gap-6 bg-gray-50 p-6 rounded-3xl border border-gray-100">
                    <div id="detail-logo" class="w-24 h-24 rounded-2xl bg-white border border-gray-200 flex items-center justify-center text-3xl font-bold text-accent shadow-sm overflow-hidden shrink-0">
                        <!-- Logo or Initials -->
                    </div>
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center justify-between">
                            <h3 id="detail-name" class="text-2xl font-bold text-gray-900"></h3>
                            <span id="detail-status" class="px-3 py-1 rounded-full text-xs font-bold border"></span>
                        </div>
                        <p id="detail-type-level" class="text-sm text-gray-500 font-bold"></p>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span id="detail-city" class="flex items-center gap-1.5"><i class="fas fa-map-marker-alt text-gray-400"></i> </span>
                            <span id="detail-email" class="flex items-center gap-1.5"><i class="fas fa-envelope text-gray-400"></i> </span>
                            <span id="detail-phone" class="flex items-center gap-1.5"><i class="fas fa-phone text-gray-400"></i> </span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-blue-50/50 rounded-2xl border border-blue-100/50 flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/20">
                            <i class="fas fa-images"></i>
                        </div>
                        <div>
                            <p class="text-xs text-blue-600 font-bold mb-0.5">عدد الألبومات</p>
                            <h4 id="stat-albums" class="text-2xl font-bold text-gray-900">0</h4>
                        </div>
                    </div>
                    <div class="p-6 bg-purple-50/50 rounded-2xl border border-purple-100/50 flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-500/20">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div>
                            <p class="text-xs text-purple-600 font-bold mb-0.5">عدد الكروت</p>
                            <h4 id="stat-cards" class="text-2xl font-bold text-gray-900">0</h4>
                        </div>
                    </div>
                    <div class="p-6 bg-orange-50/50 rounded-2xl border border-orange-100/50 flex items-center gap-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-orange-500/20">
                            <i class="fas fa-database"></i>
                        </div>
                        <div>
                            <p class="text-xs text-orange-600 font-bold mb-0.5">مساحات التخزين</p>
                            <h4 id="stat-libraries" class="text-2xl font-bold text-gray-900">0</h4>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Storage Info -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                            <span class="w-1 h-4 bg-accent rounded-full"></span>
                            <span>استهلاك المساحة</span>
                        </h4>
                        <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100 space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 font-bold">المساحة المستخدمة</span>
                                <span id="storage-percent" class="font-bold text-accent">0%</span>
                            </div>
                            <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                                <div id="storage-progress" class="h-full bg-accent transition-all duration-500" style="width: 0%"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span id="storage-used" class="text-gray-600">0 GB</span>
                                <span id="storage-total" class="text-gray-400">من أصل 0 GB</span>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Info -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                            <span class="w-1 h-4 bg-accent rounded-full"></span>
                            <span>حالة الاشتراك</span>
                        </h4>
                        <div id="subscription-card" class="p-6 bg-gray-50 rounded-2xl border border-gray-100 flex flex-col justify-center h-[134px]">
                            <!-- Subscription details injected here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <x-button type="button" onclick="closeDetailsModal()" variant="secondary">إغلاق</x-button>
            <x-button id="edit-from-details" variant="primary">تعديل البيانات</x-button>
        </x-slot>
    </x-modal>

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

            <!-- Credentials -->
            <div id="credentials-section" class="border-t border-gray-100 pt-6 mt-2 p-2">
                <h3 class="text-sm font-bold text-gray-700 mb-2">بيانات الدخول</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="username" label="اسم المستخدم" icon="fa-user" placeholder="اتركه فارغاً للتوليد التلقائي" />
                    <x-form.input name="password" label="كلمة السر" type="password" icon="fa-lock" placeholder="اتركها فارغة لاستخدام الافتراضي" />
                </div>
            </div>

            <!-- Global Status (Outside credentials so it stays visible during edit) -->
            <div class="border-t border-gray-100 pt-6 mt-2 p-2">
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
