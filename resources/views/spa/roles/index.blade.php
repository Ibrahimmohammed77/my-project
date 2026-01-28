@extends('layouts.app')

@section('title', 'إدارة الأدوار')
@section('header', 'إدارة الأدوار')

@section('content')
    <x-page-header title="إدارة الأدوار">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث عن دور..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
        
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>إضافة دور جديد</span>
        </x-button>
    </x-page-header>

    <!-- Roles Table -->
    <x-table :headers="[
        ['name' => 'الدور', 'class' => 'w-1/4'],
        ['name' => 'الوصف', 'class' => 'w-1/3'],
        ['name' => 'الصلاحيات'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="roles">
        <!-- JS renders rows here -->
    </x-table>

<!-- Create/Edit Modal -->
<div id="role-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
            <!-- Modal Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                    <span class="w-2 h-6 bg-accent rounded-full"></span>
                    <span>إضافة دور جديد</span>
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6">
                <form id="role-form" class="space-y-5">
                    <input type="hidden" id="role-id">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">اسم الدور <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-tag absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="name" required class="w-full pr-8 pl-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" placeholder="مثال: مدير المبيعات">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الوصف</label>
                        <textarea id="description" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" rows="3" placeholder="وصف وتوضيح مهام هذا الدور..."></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-row-reverse gap-3">
                <button type="submit" form="role-form" class="flex-1 sm:flex-none justify-center rounded-xl bg-accent px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-accent-hover active:scale-95 transition-all">حفظ التغييرات</button>
                <button type="button" onclick="closeModal()" class="flex-1 sm:flex-none justify-center rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 active:scale-95 transition-all">إلغاء</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/spa/pages/roles.js')
@endpush
@endsection
