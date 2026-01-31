@extends('layouts.app')
@section('title', 'إدارة مجموعات الكروت')
@section('header', 'إدارة مجموعات الكروت')

@section('content')
    <x-page-header title="إدارة مجموعات الكروت">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
        
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>مجموعة جديدة</span>
        </x-button>
    </x-page-header>

    <div id="sub-branches-container" class="mt-6">
        <div id="sub-branches-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <!-- JS renders groups here -->
        </div>
    </div>
    
    <div id="empty-state" class="hidden flex flex-col items-center justify-center py-12">
        <div class="p-4 bg-gray-50 rounded-full mb-3">
            <i class="fas fa-folder-open text-3xl text-gray-300"></i>
        </div>
        <p class="text-gray-500 font-medium">لا توجد مجموعات حتى الآن</p>
    </div>

    {{-- Hidden table for legacy support if needed, or just remove it if we fully switch --}}
    {{-- <x-table ... --}}

    {{-- نافذة إضافة/تعديل مجموعة --}}
    <x-modal id="modal" title="إضافة مجموعة جديدة">
        <form id="modal-form" class="space-y-4">
            <input type="hidden" name="id">
            
            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">اسم المجموعة</label>
                <input type="text" name="name" required placeholder="مثال: دفعة 2024 - المستوى الأول" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">الوصف</label>
                <textarea name="description" rows="3" placeholder="وصف اختياري للمجموعة..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm resize-none"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">عدد الكروت الفرعية المتاحة</label>
                <input type="number" name="sub_card_available" min="0" value="0" required placeholder="مثال: 30" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                <p class="text-xs text-gray-500">عدد الكروت الفرعية التي يمكن إنشاؤها في هذه المجموعة</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
                <x-button type="submit" variant="primary">حفظ المجموعه</x-button>
            </div>
        </form>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/modules/cards/index.js')
@endpush
@endsection
