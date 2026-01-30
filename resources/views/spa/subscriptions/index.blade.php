@extends('layouts.app')

@section('title', 'إدارة الاشتراكات')
@section('header', 'إدارة الاشتراكات')

@section('content')
    <x-page-header title="إدارة الاشتراكات">
        <div class="flex gap-4 items-center">
            <!-- Search -->
            <div class="relative min-w-[300px]">
                <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search" placeholder="بحث عن مستخدم..." class="w-full pl-4 pr-12 py-3 bg-white border border-gray-200 rounded-2xl focus:border-accent focus:ring-4 focus:ring-accent/10 outline-none transition-all text-sm shadow-sm" value="{{ $filters['search'] ?? '' }}">
            </div>
            
            <!-- Plan Filter -->
            <select id="plan-filter" class="px-4 py-3 bg-white border border-gray-200 rounded-2xl focus:border-accent outline-none text-sm shadow-sm min-w-[150px]">
                <option value="">كل الخطط</option>
                @foreach($plans as $plan)
                    <option value="{{ $plan->plan_id }}">{{ $plan->name }}</option>
                @endforeach
            </select>
        </div>
        
        <x-button onclick="showCreateModal()" variant="primary" class="shadow-lg shadow-accent/20">
            <i class="fas fa-plus text-xs"></i>
            <span>منح خطة جديدة</span>
        </x-button>
    </x-page-header>

    <!-- Subscriptions Table -->
    <x-table :headers="[
        ['name' => 'المستخدم', 'class' => 'w-1/4'],
        ['name' => 'الخطة', 'class' => 'w-1/4'],
        ['name' => 'الحالة', 'class' => 'w-1/6'],
        ['name' => 'تاريخ الانتهاء', 'class' => 'w-1/6'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="subscriptions">
        <!-- JS renders rows here -->
        <tbody id="subscriptions-tbody">
            <!-- Loading State -->
            <tr id="loading-state" class="hidden">
                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center gap-3">
                        <i class="fas fa-circle-notch fa-spin text-3xl text-accent"></i>
                        <span class="text-sm font-medium">جاري التحميل...</span>
                    </div>
                </td>
            </tr>
            <!-- Empty State handled by JS -->
        </tbody>
    </x-table>

    <!-- Empty State -->
    <div id="empty-state" class="hidden py-24 text-center">
        <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
            <i class="fas fa-subscription text-3xl text-gray-300"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900 mb-1">لا يوجد اشتراكات</h3>
        <p class="text-gray-500 text-sm max-w-xs mx-auto">لم يتم العثور على أي اشتراكات مطابقة لمعايير البحث.</p>
    </div>

<!-- Create Modal -->
<div id="subscription-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" onclick="closeModal()"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-right shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-100">
            <!-- Modal Header -->
            <div class="px-10 pt-10 pb-6 flex justify-between items-center">
                <h3 class="text-2xl font-black text-gray-900 flex items-center gap-3" id="subscription-modal-title">
                    <span class="w-2 h-8 bg-accent rounded-full"></span>
                    <span>منح خطة اشتراك</span>
                </h3>
                <button onclick="closeModal()" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-900 hover:bg-gray-100 transition-all active:scale-95">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-10 pb-10">
                <form id="subscription-form" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم المستخدم (User ID) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-user absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="number" id="user_id" required class="w-full pl-4 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:border-accent focus:ring-4 focus:ring-accent/10 outline-none transition-all text-sm font-medium" placeholder="أدخل رقم المستخدم">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">الخطة <span class="text-red-500">*</span></label>
                            <select id="plan_id" required class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:border-accent outline-none text-sm font-medium appearance-none">
                                <option value="">اختر الخطة</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->plan_id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">دورة الفوترة <span class="text-red-500">*</span></label>
                            <select id="billing_cycle" required class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-2xl focus:border-accent outline-none text-sm font-medium appearance-none">
                                <option value="monthly">شهري</option>
                                <option value="yearly">سنوي</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-4 bg-accent/5 rounded-2xl border border-accent/10">
                        <input type="checkbox" id="auto_renew" class="w-5 h-5 rounded-lg text-accent border-gray-300 focus:ring-accent transition-all" checked>
                        <label for="auto_renew" class="text-sm font-bold text-accent">تجديد تلقائي للاشتراك</label>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="px-10 py-8 bg-gray-50/50 border-t border-gray-100 flex gap-4">
                <button type="submit" form="subscription-form" class="flex-1 justify-center rounded-2xl bg-accent px-8 py-4 text-sm font-bold text-white shadow-lg shadow-accent/20 hover:bg-accent-hover active:scale-95 transition-all">حفظ الاشتراك</button>
                <button type="button" onclick="closeModal()" class="flex-1 justify-center rounded-2xl border border-gray-200 bg-white px-8 py-4 text-sm font-bold text-gray-700 hover:bg-gray-100 active:scale-95 transition-all">إلغاء</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/spa/pages/subscriptions.js')
@endpush
@endsection
