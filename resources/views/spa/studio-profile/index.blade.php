@extends('layouts.app')
@section('title', 'الملف الشخصي للاستوديو')
@section('header', 'الملف الشخصي')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form id="profile-form" class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xl font-black text-gray-800">تعديل بيانات الاستوديو</h3>
                <p class="text-sm text-gray-500 mt-1">قم بتحديث معلومات الاستوديو الخاص بك</p>
            </div>

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="name" label="اسم الاستوديو" icon="fa-building" value="{{ Auth::user()->name }}" required />
                    <x-form.input name="city" label="المدينة" icon="fa-map-marker-alt" value="{{ $studio->city }}" />
                </div>

                <x-form.input name="address" label="العنوان" icon="fa-location-dot" value="{{ $studio->address }}" />

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">وصف الاستوديو</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm transition-all">{{ $studio->description }}</textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <x-button type="submit" variant="primary">حفظ التغييرات</x-button>
                </div>
            </div>
        </form>
    </div>

@push('scripts')
    @vite('resources/js/spa/contexts/studio/profile/index.js')
@endpush
@endsection
