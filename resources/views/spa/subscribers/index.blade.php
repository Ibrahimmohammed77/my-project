@extends('layouts.app')
@section('title', 'إدارة المشتركين')
@section('header', 'إدارة المشتركين')

@section('content')
    <x-page-header title="إدارة المشتركين">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث عن مشترك..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>

        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>إضافة مشترك</span>
        </x-button>
    </x-page-header>

    <x-table :headers="[
        ['name' => 'الحساب', 'class' => 'w-1/3'],
        ['name' => 'معرف المشترك', 'class' => 'w-1/4'],
        ['name' => 'الحالة', 'class' => 'w-1/4'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="subscribers">
        <!-- JS renders rows here -->
    </x-table>

    <x-modal id="subscriber-modal" title="إضافة مشترك جديد" maxWidth="5xl">
        <form id="subscriber-form" class="space-y-4">
            <input type="hidden" id="subscriber-id" name="subscriber_id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">معرف الحساب (Account ID) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-user absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <input type="number" id="account_id" name="account_id" required placeholder="أدخل معرف الحساب" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">يجب أن يكون حساباً موجوداً في النظام.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                        <select id="subscriber_status_id" name="subscriber_status_id" required class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none transition-all hover:border-accent/50">
                            <option value="">اختر الحالة</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>
        </form>

        <x-slot name="footer">
            <x-button type="submit" form="subscriber-form" variant="primary">حفظ التغييرات</x-button>
            <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
        </x-slot>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/modules/subscribers/index.js')
@endpush
@endsection
