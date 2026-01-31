@extends('layouts.app')
@section('title', 'إدارة العملاء')
@section('header', 'إدارة العملاء')

@section('content')
    <x-page-header title="إدارة العملاء">
        <!-- Search -->
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث عن عميل..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
    </x-page-header>

    <!-- Table -->
    <x-table :headers="[
        ['name' => 'العميل'],
        ['name' => 'البريد الإلكتروني'],
        ['name' => 'رقم الجوال'],
        ['name' => 'تاريخ الانضمام'],
        ['name' => 'الحالة'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="customers-table">
        <!-- JS renders rows here -->
    </x-table>

@push('scripts')
    @vite('resources/js/spa/contexts/studio/customers/index.js')
@endpush
@endsection
