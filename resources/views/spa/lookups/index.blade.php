@extends('layouts.app')
@section('title', 'إدارة القوائم')
@section('header', 'إدارة القوائم')

@section('content')
    <x-page-header title="إدارة القوائم">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="lookups-search" placeholder="بحث عن قائمة..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
    </x-page-header>

    <x-table :headers="[
        ['name' => 'القائمة', 'class' => 'w-1/3'],
        ['name' => 'القيم'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="lookups">
        <!-- JS renders rows here -->
    </x-table>

    <!-- Generic Modal for Lookups -->
    <div id="lookup-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">إدارة القيم</h3>
                    
                    <!-- Values List -->
                    <div class="mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكود</th>
                                    <th scope="col" class="relative px-6 py-3">
                                        <span class="sr-only">إجراءات</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="values-tbody" class="bg-white divide-y divide-gray-200">
                                <!-- Values rendered here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Add/Edit Value Form -->
                    <div class="border-t pt-4">
                        <h4 class="text-md font-medium text-gray-900 mb-2">إضافة / تعديل قيمة</h4>
                        <form id="value-form" class="space-y-4">
                            <input type="hidden" id="value-id">
                            <input type="hidden" id="lookup-master-id">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="value-name" class="block text-sm font-medium text-gray-700">الاسم</label>
                                    <input type="text" id="value-name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-accent focus:border-accent sm:text-sm">
                                </div>
                                <div>
                                    <label for="value-code" class="block text-sm font-medium text-gray-700">الكود</label>
                                    <input type="text" id="value-code" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-accent focus:border-accent sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label for="value-description" class="block text-sm font-medium text-gray-700">الوصف</label>
                                <input type="text" id="value-description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-accent focus:border-accent sm:text-sm">
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick="resetValueForm()" class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:text-sm">
                                    جديد
                                </button>
                                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-accent text-base font-medium text-white hover:bg-accent-hover sm:text-sm">
                                    حفظ القيمة
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
    @vite('resources/js/spa/modules/lookups/index.js')
@endpush
@endsection
