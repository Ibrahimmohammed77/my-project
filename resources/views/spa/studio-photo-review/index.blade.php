@extends('layouts.app')
@section('title', 'مراجعة الصور')
@section('header', 'مراجعة الصور')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <x-page-header title="مراجعة الصور المعلقة">
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 font-medium">الصور بانتظار المراجعة</span>
                <span id="pending-count" class="h-6 px-2 rounded-full bg-accent/10 text-accent text-xs font-bold flex items-center justify-center">--</span>
            </div>
        </x-page-header>

        <!-- Loading State -->
        <div id="loading-state" class="py-20 flex flex-col items-center justify-center gap-4">
            <div class="relative">
                <div class="w-16 h-16 border-4 border-accent/20 border-t-accent rounded-full animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-camera text-accent text-xl"></i>
                </div>
            </div>
            <p class="text-gray-500 font-bold animate-pulse">جاري جلب الصور المعلقة...</p>
        </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden py-20 text-center">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-dashed border-gray-200">
                <i class="fas fa-check-double text-gray-300 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800">لا توجد صور للمراجعة</h3>
            <p class="text-gray-500 mt-2">لقد قمت بمراجعة جميع الصور المرفوعة بنجاح.</p>
        </div>

        <!-- Photos Grid -->
        <div id="photos-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <!-- JS renders items here -->
        </div>
    </div>

    <!-- Rejection Modal -->
    <x-modal id="rejection-modal" title="رفض الصورة">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <span class="w-2 h-6 bg-red-500 rounded-full"></span>
                <span>سبب رفض الصورة</span>
            </div>
        </x-slot>

        <form id="rejection-form" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">يرجى توضيح سبب الرفض للمشترك</label>
                <textarea id="rejection_reason" name="rejection_reason" rows="4" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:border-red-500 focus:ring-4 focus:ring-red-500/10 outline-none transition-all text-sm resize-none" placeholder="مثال: الصورة غير واضحة، أو لا تناسب معايير الجودة..."></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
                <x-button type="submit" variant="danger">تأكيد الرفض</x-button>
            </div>
        </form>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/contexts/studio/photo-review/index.js')
@endpush
@endsection
