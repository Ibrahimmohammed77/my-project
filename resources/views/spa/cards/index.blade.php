@extends('layouts.app')
@section('title', 'كروت المجموعة: ' . $group->name)
@section('header', 'إدارة الكروت')

@section('content')
    <input type="hidden" id="group-id" value="{{ $group->group_id }}">
    
    <x-page-header title="كروت المجموعة: {{ $group->name }}">
        <x-slot name="actions">
            <x-button onclick="history.back()" variant="secondary">
                <i class="fas fa-arrow-right text-xs"></i>
                <span>العودة للمجموعات</span>
            </x-button>
            <x-button onclick="showCreateCardModal()" variant="primary">
                <i class="fas fa-plus text-xs"></i>
                <span>كرت جديد</span>
            </x-button>
        </x-slot>
        
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث برقم الكرت..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
    </x-page-header>

    <x-table :headers="[
        ['name' => 'رقم الكرت / UUID', 'class' => 'w-1/3'],
        ['name' => 'النوع / الحامل'],
        ['name' => 'الحالة'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="cards">
        <!-- JS renders rows here -->
    </x-table>

@push('scripts')
    @vite('resources/js/spa/modules/cards/index.js')
@endpush
@endsection
