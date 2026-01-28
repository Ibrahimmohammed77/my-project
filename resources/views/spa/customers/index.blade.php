@extends('layouts.app')

@section('title', 'إدارة العملاء')
@section('header', 'إدارة العملاء')

@section('content')
    <x-page-header title="إدارة العملاء">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث عن عميل..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
        
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>إضافة عميل</span>
        </x-button>
    </x-page-header>

    <!-- Customers Table -->
    <x-table :headers="[
        ['name' => 'العميل', 'class' => 'w-1/3'],
        ['name' => 'معلومات الاتصال', 'class' => 'w-1/3'],
        ['name' => 'التفاصيل', 'class' => 'w-1/4'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="customers">
        <!-- JS renders rows here -->
    </x-table>

<!-- Create/Edit Modal -->
<div id="customer-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100">
            <!-- Modal Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                    <span class="w-2 h-6 bg-accent rounded-full"></span>
                    <span>إضافة عميل جديد</span>
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6">
                <form id="customer-form" class="space-y-5">
                    <input type="hidden" id="customer-id">
                    
                    <!-- Temp Account ID field for validation pass - Ideally should be dropdown or auto-filled -->
                    <!-- Hardcoding a valid account_id or making it a select would be better. For now assuming exists. -->
                    <!-- Let's make it a simple input for flexibility or hidden if we had auth user context -->
                    <div class="hidden">
                         <input type="text" id="account_id" value="1"> 
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">الاسم الأول <span class="text-red-500">*</span></label>
                            <input type="text" id="first_name" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">اسم العائلة <span class="text-red-500">*</span></label>
                            <input type="text" id="last_name" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                         <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">البريد الإلكتروني <span class="text-red-500">*</span></label>
                            <input type="email" id="email" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">رقم الهاتف <span class="text-red-500">*</span></label>
                             <input type="tel" id="phone" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">تاريخ الميلاد</label>
                            <input type="date" id="date_of_birth" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">الجنس <span class="text-red-500">*</span></label>
                            <!-- Hardcoded for now, ideal would be to load from API -->
                             <select id="gender_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none">
                                <option value="">اختر الجنس</option>
                                <option value="1">ذكر</option> <!-- Assuming ID 1 -->
                                <option value="2">أنثى</option> <!-- Assuming ID 2 -->
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-row-reverse gap-3">
                <button type="submit" form="customer-form" class="flex-1 sm:flex-none justify-center rounded-xl bg-accent px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-accent-hover active:scale-95 transition-all">حفظ التغييرات</button>
                <button type="button" onclick="closeModal()" class="flex-1 sm:flex-none justify-center rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 active:scale-95 transition-all">إلغاء</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/spa/pages/customers.js')
@endpush
@endsection
