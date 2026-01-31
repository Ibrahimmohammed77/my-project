{{-- resources/views/spa/subscriptions/index.blade.php --}}
@extends('layouts.app')
@section('title', 'إدارة الاشتراكات')
@section('header', 'إدارة الاشتراكات')

@section('content')
    <x-page-header title="إدارة الاشتراكات">
        <!-- Search -->
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث عن مستخدم..."
                   class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>

        <!-- Plan Filter -->
        <div class="relative min-w-[160px]">
            <i class="fas fa-cube absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <select id="plan-filter"
                    class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer text-gray-600">
                <option value="">كل الخطط</option>
            </select>
            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        <!-- Status Filter -->
        <div class="relative min-w-[160px]">
            <i class="fas fa-filter absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <select id="status-filter"
                    class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer text-gray-600">
                <option value="">كل الحالات</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                @endforeach
            </select>
            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        <!-- Create Button -->
        <x-button onclick="showCreateModal()" variant="primary" class="shadow-lg shadow-accent/20 hover:shadow-accent/30">
            <i class="fas fa-plus text-xs"></i>
            <span>منح اشتراك جديد</span>
        </x-button>
    </x-page-header>

    <!-- Generic Table Component -->
    <x-table :headers="[
        ['name' => 'المستخدم', 'class' => 'w-1/4'],
        ['name' => 'الخطة', 'class' => 'w-1/4'],
        ['name' => 'الحالة', 'class' => 'w-1/6'],
        ['name' => 'تاريخ الانتهاء', 'class' => 'w-1/6'],
        ['name' => 'التجديد التلقائي', 'class' => 'w-1/6'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="subscriptions">
        <!-- JS renders rows here -->
        <tbody id="subscriptions-tbody">
            <!-- Loading State -->
            <tr id="loading-state" class="hidden">
                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center gap-3">
                        <i class="fas fa-circle-notch fa-spin text-3xl text-accent"></i>
                        <span class="text-sm font-medium">جاري التحميل...</span>
                    </div>
                </td>
            </tr>

            <!-- Empty State -->
            <tr id="empty-state" class="hidden">
                <td colspan="6" class="px-6 py-24 text-center">
                    <div class="flex flex-col items-center">
                        <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                            <i class="fas fa-calendar-times text-3xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1">لا يوجد اشتراكات</h3>
                        <p class="text-gray-500 text-sm max-w-xs mx-auto">لم يتم العثور على أي اشتراكات مطابقة لمعايير البحث.</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </x-table>

    <!-- Pagination Container -->
    <div id="pagination-container" class="mt-4"></div>

    <!-- Create/Edit Modal Component -->
    <x-modal id="subscription-modal" title="منح اشتراك جديد" maxWidth="md">
        <form id="subscription-form" class="space-y-6">
            <input type="hidden" id="subscription-id">
            <input type="hidden" id="user_id" name="user_id">

            <!-- User Information -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                    <h3 class="text-sm font-bold text-gray-700">معلومات المستخدم</h3>
                </div>

                <div class="relative group">
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث عن مستخدم <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <input type="text"
                               id="user-search"
                               class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm"
                               placeholder="ابحث بالاسم أو البريد الإلكتروني"
                               autocomplete="off">
                    </div>
                    <div id="user-results" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-y-auto"></div>
                </div>

                <div id="selected-user" class="hidden p-4 bg-blue-50 border border-blue-100 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-bold text-gray-900" id="selected-user-name"></h4>
                            <p class="text-xs text-gray-500" id="selected-user-email"></p>
                        </div>
                        <button type="button" onclick="subscriptionController.view.clearUserSelection()" class="text-gray-400 hover:text-red-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Subscription Details -->
            <div class="space-y-4 pt-4 border-t border-gray-100">
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-1 h-5 bg-green-500 rounded-full"></span>
                    <h3 class="text-sm font-bold text-gray-700">تفاصيل الاشتراك</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الخطة <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-cube absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <select id="plan_id" name="plan_id" required
                                    class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                                <option value="">اختر الخطة</option>
                            </select>
                            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <div id="status-field-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <div class="relative">
                            <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <select id="status_id" name="status_id"
                                    class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">دورة الفوترة <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <select id="billing_cycle" name="billing_cycle" required
                                    class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                                <option value="monthly">شهري</option>
                                <option value="yearly">سنوي</option>
                            </select>
                            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <!-- Price Display -->
                <div id="price-display" class="hidden p-4 bg-green-50 border border-green-100 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">السعر:</span>
                        <div class="flex items-center gap-1">
                            <span class="text-lg font-bold text-green-600" id="selected-price">0.00</span>
                            <span class="text-sm text-gray-500" id="price-period">/شهر</span>
                        </div>
                    </div>
                </div>

                <!-- Auto Renew -->
                <div class="flex items-center gap-3 p-4 bg-accent/5 rounded-xl border border-accent/10">
                    <input type="checkbox"
                           id="auto_renew"
                           name="auto_renew"
                           class="w-5 h-5 rounded text-accent border-gray-300 focus:ring-accent transition-all cursor-pointer"
                           checked>
                    <label for="auto_renew" class="text-sm font-medium text-gray-700 cursor-pointer">
                        <span class="font-bold text-accent">التجديد التلقائي</span>
                        <span class="text-xs text-gray-500 block mt-1">سيتم تجديد الاشتراك تلقائياً قبل انتهائه</span>
                    </label>
                </div>
            </div>

            <!-- Duration Info -->
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <h4 class="text-sm font-bold text-gray-700">معلومات المدة</h4>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="text-center p-3 bg-white rounded-lg border border-gray-100">
                        <div class="text-xs text-gray-500 mb-1">تاريخ البدء</div>
                        <div class="font-bold text-gray-900" id="start-date">اليوم</div>
                    </div>
                    <div class="text-center p-3 bg-white rounded-lg border border-gray-100">
                        <div class="text-xs text-gray-500 mb-1">تاريخ الانتهاء</div>
                        <div class="font-bold text-gray-900" id="end-date">-</div>
                    </div>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-button type="submit" form="subscription-form" variant="primary" class="w-full justify-center">
                <i class="fas fa-check ml-2"></i>
                <span>حفظ الاشتراك</span>
            </x-button>
            <x-button type="button" onclick="closeModal()" variant="secondary" class="w-full justify-center">
                <i class="fas fa-times ml-2"></i>
                <span>إلغاء</span>
            </x-button>
        </x-slot>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/modules/subscriptions/index.js')
@endpush
@endsection
