@extends('layouts.app')
@section('title', 'ألبومات المدرسة')
@section('header', 'ألبومات المدرسة')

@section('content')
    <x-page-header title="ألبومات المدرسة">
        <!-- Search -->
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث عن ألبوم..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>

        <!-- Create Button -->
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>ألبوم مدرسي جديد</span>
        </x-button>
    </x-page-header>

    <!-- Table -->
    <x-table :headers="[
        ['name' => 'الألبوم'],
        ['name' => 'الوصف'],
        ['name' => 'عدد الصور'],
        ['name' => 'الحالة'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="albums-table">
        <!-- JS renders rows here -->
    </x-table>

    <!-- Modal -->
    <x-modal id="album-modal" title="ألبوم مدرسي جديد">
        <form id="album-form" class="space-y-4">
            <input type="hidden" id="album-id">
            
            <x-form.input name="name" label="اسم الألبوم" icon="fa-school" placeholder="مثلاً: حفل تخرج 2026" required />
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وصف الألبوم</label>
                <textarea id="description" name="description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm transition-all" placeholder="اكتب وصفاً قصيراً للفعالية المدرسية..."></textarea>
            </div>

            <div class="flex items-center gap-2 py-2">
                <input type="checkbox" id="is_visible" name="is_visible" value="1" checked class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                <label for="is_visible" class="text-sm text-gray-700 font-medium">عرض الألبوم للطلاب</label>
            </div>
            
            <!-- Note for school owner -->
            <div class="p-3 bg-blue-50 border border-blue-100 rounded-xl">
                <div class="flex gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <p class="text-xs text-blue-700 leading-relaxed">
                        سيتم تخصيص مساحة التخزين تلقائياً لهذا الألبوم بناءً على خطة المدرسة النشطة.
                    </p>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-button type="submit" form="album-form" variant="primary">حفظ الألبوم</x-button>
            <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
        </x-slot>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/pages/school-albums.js')
@endpush
@endsection
