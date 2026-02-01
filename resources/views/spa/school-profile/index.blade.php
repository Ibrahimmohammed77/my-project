@extends('layouts.app')
@section('title', 'الملف الشخصي للمدرسة')
@section('header', 'بيانات المدرسة')

@section('content')
    <div class="max-w-4xl mx-auto">
        <form id="school-profile-form" class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden" enctype="multipart/form-data">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black text-gray-800">تعديل بيانات المدرسة</h3>
                    <p class="text-sm text-gray-500 mt-1">إدارة معلومات مؤسستك التعليمية</p>
                </div>
                <div class="w-16 h-16 rounded-2xl bg-accent/5 flex items-center justify-center border border-accent/10">
                    <i class="fas fa-school-flag text-2xl text-accent"></i>
                </div>
            </div>

            <!-- Profile Image Section -->
            <div class="px-8 pt-8 flex justify-center">
                <div class="relative w-32 h-32 group">
                    @php $profileImage = Auth::user()->profile_image ? asset('storage/'.Auth::user()->profile_image) : null; @endphp
                    <img id="profile-image-preview" src="{{ $profileImage ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=EBF4FF&color=7F9CF5' }}" 
                         alt="School Logo" 
                         class="w-full h-full rounded-2xl object-cover border-4 border-white shadow-lg group-hover:shadow-xl transition-all">
                    
                    <label for="profile_image" class="absolute -bottom-2 -right-2 w-10 h-10 bg-accent text-white rounded-xl flex items-center justify-center cursor-pointer shadow-lg hover:bg-accent-dark transition-colors border-2 border-white">
                        <i class="fas fa-camera text-sm"></i>
                        <input type="file" id="profile_image" name="profile_image" class="hidden" accept="image/*">
                    </label>
                </div>
            </div>

            <div class="p-8 space-y-6">
                <!-- Name & City -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input name="name" label="اسم المدرسة الرسمي" icon="fa-school" value="{{ Auth::user()->name }}" required />
                    <x-form.input name="city" label="المدينة / المنطقة" icon="fa-map-marker-alt" value="{{ $school->city }}" />
                </div>

                <!-- Full Address -->
                <x-form.input name="address" label="العنوان التفصيلي" icon="fa-location-dot" value="{{ $school->address }}" />

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عن المدرسة / الوصف</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm transition-all" placeholder="اكتب نبذة عن تاريخ المدرسة ورسالتها...">{{ $school->description }}</textarea>
                </div>

                <!-- Action Button -->
                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <x-button type="submit" variant="primary" class="w-full md:w-auto">
                        <i class="fas fa-save text-xs"></i>
                        <span>حفظ التغييرات</span>
                    </x-button>
                </div>
            </div>
        </form>
    </div>

@push('scripts')
    @vite('resources/js/spa/contexts/school/profile/index.js')
@endpush
@endsection
