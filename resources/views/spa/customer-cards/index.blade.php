@extends('layouts.app')

@section('title', 'كروتي')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ showModal: false, showLinkModal: false }">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">كروتي</h1>
        <p class="mt-2 text-gray-600">إدارة الكروت وربطها بالألبومات</p>
    </div>

    <!-- Actions Bar -->
    <div class="bg-white rounded-xl shadow-soft p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
            <!-- Search -->
            <div class="relative flex-1 w-full sm:max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="بحث في الكروت..." 
                    class="w-full pr-12 pl-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent transition-all"
                >
            </div>

            <!-- Create Button -->
            <button 
                @click="showModal = true"
                class="btn-primary flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-plus"></i>
                <span>كرت جديد</span>
            </button>
        </div>
    </div>

    <!-- Cards Table -->
    <div class="bg-white rounded-xl shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">العنوان</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">رمز QR</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">عدد الألبومات</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">الحالة</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="cardsTableBody" class="divide-y divide-gray-100">
                    <!-- Will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <!-- Will be populated by JavaScript -->
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <x-card-modal />

    <!-- Link Albums Modal -->
    <x-card-link-albums-modal />
</div>

@push('scripts')
    @vite(['resources/js/spa/contexts/customer/cards/index.js'])
@endpush
@endsection
