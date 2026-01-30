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

    <!-- Plan Modal -->
    <div id="plan-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">إضافة خطة جديدة</h3>
                            <div class="mt-4">
                                <form id="plan-form" class="space-y-4">
                                    <input type="hidden" id="plan-id">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">اسم الخطة</label>
                                        <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-accent focus:border-accent sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700">الوصف</label>
                                        <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-accent focus:border-accent sm:text-sm"></textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="price_monthly" class="block text-sm font-medium text-gray-700">سعر شهري</label>
                                            <input type="number" step="0.01" name="price_monthly" id="price_monthly" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-accent focus:border-accent sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="price_yearly" class="block text-sm font-medium text-gray-700">سعر سنوي</label>
                                            <input type="number" step="0.01" name="price_yearly" id="price_yearly" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-accent focus:border-accent sm:text-sm">
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-accent focus:ring-accent border-gray-300 rounded">
                                        <label for="is_active" class="mr-2 block text-sm text-gray-900">نشط</label>
                                    </div>
                                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-accent text-base font-medium text-white hover:bg-accent-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent sm:col-start-2 sm:text-sm">
                                            حفظ
                                        </button>
                                        <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                            إلغاء
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    @vite('resources/js/spa/pages/plans.js')
@endpush
@endsection
