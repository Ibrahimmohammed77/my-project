@extends('layouts.app')
@section('title', 'إدارة الحسابات')
@section('header', 'إدارة الحسابات')

@section('content')
    <x-page-header title="إدارة الحسابات">
        <!-- Search -->
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث بالاسم أو البريد..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>

        <!-- Status Filter -->
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
            <span>حساب جديد</span>
        </x-button>
    </x-page-header>

    <!-- Generic Table Component -->
    <x-table :headers="[
        ['name' => 'المستخدم', 'class' => 'w-1/4'],
        ['name' => 'معلومات الاتصال'],
        ['name' => 'الحالة'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="accounts">
        <!-- JS renders rows here -->
        <tbody id="accounts-tbody">
            <!-- Loading State -->
            <tr id="loading-state" class="hidden">
                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center gap-3">
                        <i class="fas fa-circle-notch fa-spin text-3xl text-accent"></i>
                        <span class="text-sm font-medium">جاري التحميل...</span>
                    </div>
                </td>
            </tr>

            <!-- Empty State -->
            <tr id="empty-state" class="hidden">
                <td colspan="4" class="px-6 py-24 text-center">
                    <div class="flex flex-col items-center">
                        <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                            <i class="fas fa-users text-3xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">لا توجد حسابات</h3>
                        <p class="text-gray-500 text-sm max-w-xs mx-auto">لم يتم العثور على أي حسابات مطابقة لمعايير البحث.</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </x-table>

    <!-- Pagination Container for JS -->
    <div id="pagination-container" class="mt-4"></div>

    <!-- Account Details Modal -->
    <x-modal id="account-details-modal" title="تفاصيل الحساب" maxWidth="4xl">
        <div id="account-details-content" class="p-6 space-y-8 text-right">
            <!-- Loading State -->
            <div id="account-details-loading" class="flex flex-col items-center justify-center py-12">
                <i class="fas fa-circle-notch fa-spin text-4xl text-accent mb-4"></i>
                <p class="text-gray-500 font-bold">جاري تحميل البيانات...</p>
            </div>

            <!-- Details Content -->
            <div id="account-details-data" class="hidden space-y-8">
                <!-- Profile Header -->
                <div class="flex items-start gap-6 bg-gray-50 p-6 rounded-3xl border border-gray-100">
                    <div id="detail-avatar" class="w-24 h-24 rounded-2xl bg-white border border-gray-200 flex items-center justify-center text-3xl font-bold text-accent shadow-sm shrink-0 overflow-hidden">
                        <!-- Avatar or Initials -->
                    </div>
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center justify-between">
                            <h3 id="detail-full_name" class="text-2xl font-bold text-gray-900"></h3>
                            <span id="detail-status" class="px-3 py-1 rounded-full text-xs font-bold border"></span>
                        </div>
                        <p id="detail-username" class="text-sm text-gray-500 font-bold"></p>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <span id="detail-email" class="flex items-center gap-1.5"><i class="fas fa-envelope text-gray-400"></i> </span>
                            <span id="detail-phone" class="flex items-center gap-1.5"><i class="fas fa-phone text-gray-400"></i> </span>
                        </div>
                    </div>
                </div>

                <!-- Roles Section -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <span class="w-1 h-4 bg-accent rounded-full"></span>
                        <span>الأدوار والصلاحيات</span>
                    </h4>
                    <div id="detail-roles" class="flex flex-wrap gap-2">
                        <!-- Roles list -->
                    </div>
                </div>

                <!-- Active Sessions / History (Placeholder) -->
                <div class="space-y-4">
                    <h4 class="text-sm font-bold text-gray-700 flex items-center gap-2">
                        <span class="w-1 h-4 bg-accent rounded-full"></span>
                        <span>آخر نشاط</span>
                    </h4>
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <div class="flex items-center gap-3 text-sm">
                            <i class="fas fa-history text-gray-400"></i>
                            <span id="detail-last-login" class="text-gray-600">غير متاح حالياً</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <x-button type="button" onclick="closeDetailsModal()" variant="secondary">إغلاق</x-button>
            <x-button id="edit-from-details" variant="primary">تعديل الحساب</x-button>
        </x-slot>
    </x-modal>

    <!-- Create/Edit Modal -->
    <x-modal id="account-modal" title="إضافة حساب جديد" maxWidth="lg">
        <form id="account-form" class="space-y-6">
            <input type="hidden" id="account-id" name="account_id">

            <!-- Basic Information -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-1 h-5 bg-accent rounded-full"></span>
                    <h3 class="text-sm font-bold text-gray-700">المعلومات الأساسية</h3>
                </div>

                <x-form.input id="full_name" name="full_name" label="الاسم الكامل" icon="fa-id-card" placeholder="الاسم الكامل" required />
                <x-form.input id="username" name="username" label="اسم المستخدم" icon="fa-user" placeholder="اسم المستخدم" required />
                <x-form.input id="email" name="email" type="email" label="البريد الإلكتروني" icon="fa-envelope" placeholder="example@domain.com" required />
                <x-form.input id="phone" name="phone" label="رقم الهاتف" icon="fa-phone" placeholder="05XXXXXXXX" />

                <div class="grid grid-cols-2 gap-4">
                    <!-- Role (Locked to Customer for this view) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الدور <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user-shield absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="role_id" name="role_id" class="w-full pl-4 pr-10 py-3 bg-gray-200 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none cursor-not-allowed" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}" data-role-name="{{ $role->name }}" @if($role->name === 'customer') selected @endif>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">حالة الحساب <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="user_status_id" name="user_status_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div id="password-field" class="border-t border-gray-100 pt-6">
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-1 h-5 bg-orange-500 rounded-full"></span>
                    <h3 class="text-sm font-bold text-gray-700">كلمة المرور</h3>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <x-form.input id="password" name="password" type="password" label="كلمة المرور" icon="fa-lock" placeholder="********" required />
                    <x-form.input id="password_confirmation" name="password_confirmation" type="password" label="تأكيد كلمة المرور" icon="fa-lock" placeholder="********" required />
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-button type="submit" form="account-form" variant="primary" class="w-full justify-center">حفظ الحساب</x-button>
            <x-button type="button" onclick="closeModal()" variant="secondary" class="w-full justify-center">إلغاء</x-button>
        </x-slot>
    </x-modal>
@endsection

@push('scripts')
    @vite('resources/js/spa/modules/accounts/index.js')
@endpush
