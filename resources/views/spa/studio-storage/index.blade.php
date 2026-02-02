@extends('layouts.app')
@section('title', 'مكتبة التخزين')
@section('header', 'مكتبة التخزين')

@section('content')
    <x-page-header title="مكتبة التخزين">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث في المكتبات..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
        
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>تخصيص مساحة جديدة</span>
        </x-button>
    </x-page-header>

    <x-table :headers="[
        ['name' => 'المكتبة'],
        ['name' => 'المشترك'],
        ['name' => 'استهلاك المساحة'],
        ['name' => 'تاريخ الإنشاء'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="storage-table">
        <!-- JS renders rows here -->
    </x-table>

    <!-- Create/Edit Modal -->
    <x-modal id="storage-modal" title="تخصيص مساحة">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <span class="w-2 h-6 bg-accent rounded-full"></span>
                <span id="modal-title">تخصيص مساحة جديدة</span>
            </div>
        </x-slot>

        <form id="storage-form" class="space-y-4">
            <input type="hidden" id="library-id" name="id">

            <x-form.input name="name" id="name" label="اسم المكتبة" icon="fa-folder" placeholder="مثلاً: مكتبة صور التخرج 2024" required />
            
            <x-form.input type="number" name="storage_limit" id="storage_limit" label="المساحة المخصصة (ميجا بايت)" icon="fa-database" placeholder="اختياري - اتركه فارغاً لغير محدود" step="0.1" />
            <p class="text-[10px] text-gray-400 -mt-2">💡 نصيحة: 1024MB = 1GB | سيتم إنشاء ألبوم مخفي تلقائياً</p>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">الوصف</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm transition-all" placeholder="وصف اختياري للمكتبة..."></textarea>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-700">
                <i class="fas fa-info-circle"></i>
                <strong>ملاحظة:</strong> سيتم إنشاء ألبوم مخفي تلقائياً عند إنشاء المكتبة. يمكنك ربط الكروت بهذه المكتبة لاحقاً.
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
                <x-button type="submit" variant="primary">
                    <i class="fas fa-save"></i> حفظ البيانات
                </x-button>
            </div>
        </form>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/contexts/studio/storage/index.js')
@endpush
@endsection
