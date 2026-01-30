@extends('layouts.app')
@section('title', 'كروت المدرسة')
@section('header', 'إدارة الكروت المدرسية')

@section('content')
    <x-page-header title="الكروت المدرسية المتاحة">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث برقم الكرت..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
    </x-page-header>

    <x-table :headers="[
        ['name' => 'عنوان الكرت'],
        ['name' => 'رقم الكرت'],
        ['name' => 'النوع'],
        ['name' => 'تاريخ الإصدار'],
        ['name' => 'الطلاب المفعلين'],
        ['name' => 'ربط ألبومات', 'class' => 'text-center']
    ]" id="cards-table">
        <!-- JS renders rows here -->
    </x-table>

    <!-- Link Albums Modal -->
    <x-modal id="link-albums-modal" title="ربط الكرت بألبومات المدرسة">
        <form id="link-albums-form" class="space-y-4">
            <input type="hidden" id="card-id-to-link">
            
            <div id="school-albums-list" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[400px] overflow-y-auto p-1">
                <!-- Checkboxes will be rendered here by JS -->
                <div class="py-10 text-center col-span-full">
                    <i class="fas fa-spinner fa-spin text-accent text-xl"></i>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-button type="submit" form="link-albums-form" variant="primary">حفظ الربط</x-button>
            <x-button type="button" onclick="window.closeModal('link-albums-modal')" variant="secondary">إلغاء</x-button>
        </x-slot>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/pages/school-cards.js')
@endpush
@endsection
