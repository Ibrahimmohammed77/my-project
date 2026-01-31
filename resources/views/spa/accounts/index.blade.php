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
                @foreach($statuses as $status)
                    <option value="{{ $status->lookup_value_id }}" @if(request('status_id') == $status->lookup_value_id) selected @endif>
                        {{ $status->name }}
                    </option>
                @endforeach
            </select>
            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        <!-- Export Button -->
        <x-button onclick="accountController.exportAccounts('csv')" variant="secondary">
            <i class="fas fa-file-export text-xs"></i>
            <span>تصدير</span>
        </x-button>

        <!-- Create Button -->
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>حساب جديد</span>
        </x-button>
    </x-page-header>

    <!-- Table Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Loading State -->
        <div id="loading-state" class="hidden p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-accent border-t-transparent"></div>
            <p class="mt-4 text-gray-500">جاري تحميل الحسابات...</p>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden p-12 text-center">
            <i class="fas fa-exclamation-circle text-red-500 text-4xl"></i>
            <p id="error-message" class="mt-4 text-red-600">حدث خطأ في تحميل البيانات</p>
            <button onclick="accountController.refresh()" class="mt-4 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                <i class="fas fa-redo mr-2"></i>إعادة المحاولة
            </button>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden p-12 text-center">
            <i class="fas fa-users text-gray-300 text-4xl"></i>
            <p class="mt-4 text-gray-500">لا توجد حسابات لعرضها</p>
            <button onclick="showCreateModal()" class="mt-4 px-4 py-2 bg-accent text-white rounded-lg hover:bg-accent/90 transition-colors">
                <i class="fas fa-plus mr-2"></i>إضافة حساب جديد
            </button>
        </div>

        <!-- Table -->
        <div id="table-container" class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">المستخدم</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">معلومات الاتصال</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-700">الأدوار</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="accounts-tbody">
                    <!-- JS will render rows here -->
                </tbody>
            </table>
        </div>

        <!-- Pagination (if using server-side) -->
        @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    <x-modal id="account-modal" title="إضافة حساب جديد" maxWidth="5xl">
        <form id="account-form" class="space-y-6">
            @csrf
            <input type="hidden" id="account-id" name="account_id">

            <!-- Basic Information Section -->
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">المعلومات الأساسية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <x-form.input
                        id="username"
                        name="username"
                        label="اسم المستخدم"
                        icon="fa-user"
                        placeholder="اسم المستخدم"
                        required
                        autocomplete="username"
                    />

                    <x-form.input
                        id="full_name"
                        name="full_name"
                        label="الاسم الكامل"
                        icon="fa-id-card"
                        placeholder="الاسم الكامل"
                        required
                    />

                    <x-form.input
                        id="email"
                        name="email"
                        type="email"
                        label="البريد الإلكتروني"
                        icon="fa-envelope"
                        placeholder="example@domain.com"
                        required
                        autocomplete="email"
                    />

                    <x-form.input
                        id="phone"
                        name="phone"
                        label="رقم الهاتف"
                        icon="fa-phone"
                        placeholder="05XXXXXXXX"
                        autocomplete="tel"
                    />

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            الدور <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-user-shield absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="role_id" name="role_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50" required>
                                <option value="">اختر الدور...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}" data-role-name="{{ $role->name }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                        <p id="role_id-error" class="text-red-500 text-xs mt-1 hidden field-error"></p>
                    </div>

                    <!-- Status Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            حالة الحساب <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="user_status_id" name="user_status_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50" required>
                                <option value="">اختر الحالة...</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->lookup_value_id }}">
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                        <p id="user_status_id-error" class="text-red-500 text-xs mt-1 hidden field-error"></p>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div id="password-field" class="border-t border-gray-100 pt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">كلمة المرور</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.input
                        id="password"
                        name="password"
                        type="password"
                        label="كلمة المرور"
                        icon="fa-lock"
                        placeholder="********"
                        autocomplete="new-password"
                        required
                    />

                    <x-form.input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        label="تأكيد كلمة المرور"
                        icon="fa-lock"
                        placeholder="********"
                        autocomplete="new-password"
                        required
                    />
                </div>
                <p class="text-xs text-gray-500 mt-2">يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل وتشمل أحرف كبيرة وصغيرة وأرقام</p>
            </div>

            <!-- Conditional Fields -->

            <!-- Studio Fields -->
            <div id="studio-fields" class="hidden border-t border-gray-100 pt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-photo-video text-purple-500 mr-2"></i>
                    معلومات الاستوديو
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form.input
                        id="studio_name"
                        name="studio_name"
                        label="اسم الاستوديو"
                        icon="fa-building"
                        placeholder="اسم الاستوديو"
                    />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">حالة الاستوديو</label>
                        <div class="relative">
                            <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="studio_status_id" name="studio_status_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                                <option value="">اختر حالة الاستوديو...</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->lookup_value_id }}">
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <x-form.input
                        id="city"
                        name="city"
                        label="المدينة"
                        icon="fa-map-marker-alt"
                        placeholder="المدينة"
                    />

                    <x-form.input
                        id="address"
                        name="address"
                        label="العنوان"
                        icon="fa-home"
                        placeholder="العنوان بالتفصيل"
                    />
                </div>
            </div>

            <!-- School Fields -->
            <div id="school-fields" class="hidden border-t border-gray-100 pt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-school text-blue-500 mr-2"></i>
                    معلومات المدرسة
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-form.input
                        id="school_name"
                        name="school_name"
                        label="اسم المدرسة"
                        icon="fa-school"
                        placeholder="اسم المدرسة"
                    />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نوع المدرسة</label>
                        <div class="relative">
                            <i class="fas fa-school absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="school_type_id" name="school_type_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                                <option value="">اختر النوع...</option>
                                @foreach($schoolTypes as $type)
                                    <option value="{{ $type->lookup_value_id }}">
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المرحلة الدراسية</label>
                        <div class="relative">
                            <i class="fas fa-graduation-cap absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="school_level_id" name="school_level_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                                <option value="">اختر المرحلة...</option>
                                @foreach($schoolLevels as $level)
                                    <option value="{{ $level->lookup_value_id }}">
                                        {{ $level->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">حالة المدرسة</label>
                        <div class="relative">
                            <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="school_status_id" name="school_status_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                                <option value="">اختر الحالة...</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->lookup_value_id }}">
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <x-form.input
                        id="city"
                        name="city"
                        label="المدينة"
                        icon="fa-map-marker-alt"
                        placeholder="المدينة"
                    />

                    <x-form.input
                        id="address"
                        name="address"
                        label="العنوان"
                        icon="fa-home"
                        placeholder="العنوان بالتفصيل"
                    />
                </div>
            </div>

            <!-- Subscriber Fields -->
            <div id="subscriber-fields" class="hidden border-t border-gray-100 pt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-tag text-green-500 mr-2"></i>
                    معلومات المشترك
                </h3>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-green-700 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        لا توجد معلومات إضافية مطلوبة للمشتركين. يمكن إضافة معلومات إضافية لاحقاً من خلال صفحة الملف الشخصي.
                    </p>
                </div>
            </div>

            <!-- Form Errors Container -->
            <div id="form-errors" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
                <h4 class="font-bold text-red-700 mb-2">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    يرجى تصحيح الأخطاء التالية:
                </h4>
                <ul id="error-list" class="text-red-600 text-sm space-y-1"></ul>
            </div>
        </form>

        <x-slot name="footer">
            <div class="flex items-center justify-between w-full">
                <div class="text-sm text-gray-500">
                    <span id="form-mode-indicator">جاري إنشاء حساب جديد</span>
                </div>
                <div class="flex items-center gap-3">
                    <x-button
                        type="button"
                        onclick="closeModal()"
                        variant="secondary"
                        class="min-w-[100px]"
                    >
                        <i class="fas fa-times mr-2"></i>إلغاء
                    </x-button>
                    <x-button
                        type="submit"
                        form="account-form"
                        variant="primary"
                        class="min-w-[100px]"
                        id="submit-button"
                    >
                        <i class="fas fa-save mr-2"></i>حفظ
                    </x-button>
                </div>
            </div>
        </x-slot>
    </x-modal>
@endsection

@push('scripts')
    @vite('resources/js/spa/modules/accounts/index.js')
@endpush
