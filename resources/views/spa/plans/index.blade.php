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
        ['name' => 'الحالة'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="plans">
        <!-- JS renders rows here -->
    </x-table>

@push('scripts')
    @vite('resources/js/spa/pages/plans.js')
@endpush
@endsection
