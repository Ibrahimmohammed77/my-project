@extends('layouts.app')
@section('title', 'إدارة خطط الاشتراك')
@section('header', 'إدارة خطط الاشتراك')

@section('content')
    <x-page-header title="إدارة خطط الاشتراك">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث عن خطة..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
        
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>خطة جديدة</span>
        </x-button>
    </x-page-header>

    <x-table :headers="[
        ['name' => 'الخطة', 'class' => 'w-1/3'],
        ['name' => 'الأسعار'],
        ['name' => 'المساحة'],
        ['name' => 'الحالة'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="plans">
        <!-- JS renders rows here -->
    </x-table>

    <!-- Plan Modal -->
    <div id="plan-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                <!-- Modal Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                        <span class="w-2 h-6 bg-accent rounded-full"></span>
                        <span>إضافة خطة جديدة</span>
                    </h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-6 text-right">
                    <form id="plan-form" class="space-y-5">
                        <input type="hidden" id="plan-id">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 ml-auto">اسم الخطة <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="fas fa-layer-group absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" id="name" required class="w-full pr-8 pl-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" placeholder="مثال: الخطة الذهبية">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 ml-auto">الوصف</label>
                            <textarea id="description" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" rows="3" placeholder="مواصفات ومميزات هذه الخطة..."></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5 ml-auto">السعر الشهري (ريال) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fas fa-calendar-alt absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="number" step="0.01" id="price_monthly" required class="w-full pr-8 pl-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5 ml-auto">السعر السنوي (ريال) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i class="fas fa-calendar-check absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="number" step="0.01" id="price_yearly" required class="w-full pr-8 pl-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 ml-auto">مساحة التخزين (جيجابايت) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="fas fa-hdd absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="number" id="storage_limit" required class="w-full pr-8 pl-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" placeholder="مثال: 50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5 ml-auto">المميزات (واحدة في كل سطر)</label>
                            <textarea id="features" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" rows="3" placeholder="ميزة 1&#10;ميزة 2&#10;ميزة 3..."></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <label for="is_active" class="text-sm font-bold text-gray-700 cursor-pointer">تفعيل الخطة</label>
                            <div class="relative inline-flex h-6 w-11 items-center cursor-pointer">
                                <input type="checkbox" id="is_active" class="sr-only peer">
                                <div onclick="document.getElementById('is_active').click()" class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-accent transition-colors"></div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-row-reverse gap-3">
                    <button type="submit" form="plan-form" class="flex-1 sm:flex-none justify-center rounded-xl bg-accent px-8 py-2.5 text-sm font-bold text-white shadow-lg shadow-accent/20 hover:bg-accent-hover active:scale-95 transition-all">حفظ البيانات</button>
                    <button type="button" onclick="closeModal()" class="flex-1 sm:flex-none justify-center rounded-xl border border-gray-200 bg-white px-8 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 active:scale-95 transition-all">إلغاء</button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    @vite('resources/js/spa/modules/plans/index.js')
@endpush
@endsection
