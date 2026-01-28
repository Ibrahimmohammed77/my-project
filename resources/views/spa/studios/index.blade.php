@extends('layouts.app')
@section('title', 'إدارة الاستوديوهات')
@section('header', 'إدارة الاستوديوهات')

@section('content')
    <x-page-header title="إدارة الاستوديوهات">
        <!-- Search -->
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>

        <!-- Filter -->
        <div class="relative min-w-[160px]">
            <i class="fas fa-filter absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <select id="status-filter" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer text-gray-600">
                <option value="">جميع الحالات</option>
                @foreach($statuses as $status)
                <option value="{{ $status->code }}">{{ $status->name }}</option>
                @endforeach
            </select>
            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>
        
        <!-- Create Button -->
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>استوديو جديد</span>
        </x-button>
    </x-page-header>

    <!-- Generic Table Component -->
    <x-table :headers="[
        ['name' => 'الاستوديو', 'class' => 'w-1/4'],
        ['name' => 'معلومات الاتصال'],
        ['name' => 'الحالة'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="studios">
        <!-- JS renders rows here -->
    </x-table>

    <!-- Create/Edit Modal Component -->
    <x-modal id="studio-modal" title="إضافة استوديو جديد">
        <form id="studio-form" class="space-y-4">
            <input type="hidden" id="studio-id">
            
            <x-form.input name="name" label="اسم الاستوديو" required icon="fa-building" placeholder="اسم الاستوديو" />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form.input name="email" label="البريد الإلكتروني" type="email" icon="fa-envelope" placeholder="example@studio.com" />
                <x-form.input name="phone" label="رقم الهاتف" icon="fa-phone" placeholder="05xxxxxxxx" />
            </div>

            <x-form.input name="website" label="الموقع الإلكتروني" icon="fa-globe" placeholder="https://example.com" />

            <!-- Status Field -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">حالة الاستوديو <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <select id="studio_status_id" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer">
                        @foreach($statuses as $status)
                        <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-button type="submit" form="studio-form" variant="primary">حفظ التغييرات</x-button>
            <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
        </x-slot>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/pages/studios.js')
@endpush
@endsection
