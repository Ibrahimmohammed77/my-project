@extends('layouts.app')
@section('title', 'إدارة الكروت')
@section('header', 'الكروت المشحونة')

@section('content')
    <x-page-header title="الكروت المشحونة">
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
        ['name' => 'الحالة'],
        ['name' => 'ربط ألبومات', 'class' => 'text-center']
    ]" id="cards-table">
        <!-- JS renders rows here -->
    </x-table>

@push('scripts')
    @vite('resources/js/spa/contexts/studio/cards/index.js')
@endpush
@endsection
