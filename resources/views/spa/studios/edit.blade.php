@extends('layouts.app')
@section('title', 'تعديل استوديو')

@section('content')
    <x-page-header title="تعديل بيانات الاستوديو">
        <x-button onclick="window.location='{{ route('spa.studios') }}'" variant="secondary">
            <i class="fas fa-arrow-right text-xs"></i>
            <span>العودة</span>
        </x-button>
    </x-page-header>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl shadow-card p-8">
            <form id="studio-form" action="{{ route('admin.studios.update', $studio->studio_id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-1 h-5 bg-accent rounded-full"></span>
                        <h3 class="text-sm font-bold text-gray-700">المعلومات الأساسية</h3>
                    </div>

                    <x-form.input 
                        id="full_name" 
                        name="full_name" 
                        label="اسم الاستوديو" 
                        icon="fa-building" 
                        value="{{ old('full_name', $studio->user->name ?? '') }}"
                        required 
                    />
                    
                    <x-form.input 
                        id="email" 
                        name="email" 
                        type="email" 
                        label="البريد الإلكتروني" 
                        icon="fa-envelope" 
                        value="{{ old('email', $studio->user->email ?? '') }}"
                        required 
                    />
                    
                    <x-form.input 
                        id="phone" 
                        name="phone" 
                        label="رقم الهاتف" 
                        icon="fa-phone" 
                        value="{{ old('phone', $studio->user->phone ?? '') }}"
                        required 
                    />

                    <x-form.input 
                        id="city" 
                        name="city" 
                        label="المدينة" 
                        icon="fa-location-dot" 
                        value="{{ old('city', $studio->city) }}"
                    />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                        <div class="relative">
                            <i class="fas fa-map-marker-alt absolute right-3 top-3 text-gray-400"></i>
                            <textarea 
                                id="address" 
                                name="address" 
                                rows="3" 
                                class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm resize-none"
                                placeholder="العنوان التفصيلي"
                            >{{ old('address', $studio->address) }}</textarea>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <div class="relative">
                            <i class="fas fa-align-right absolute right-3 top-3 text-gray-400"></i>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="3" 
                                class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm resize-none"
                                placeholder="وصف مختصر عن الاستوديو"
                            >{{ old('description', $studio->description) }}</textarea>
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">حالة الحساب <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                            <select id="user_status_id" name="user_status_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->lookup_value_id }}" @if($studio->user->user_status_id == $status->lookup_value_id) selected @endif>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </div>

                <!-- Logo Upload -->
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-1 h-5 bg-blue-500 rounded-full"></span>
                        <h3 class="text-sm font-bold text-gray-700">الشعار</h3>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">شعار الاستوديو</label>
                        <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-accent file:text-white hover:file:bg-accent/90 cursor-pointer">
                        @if($studio->logo)
                            <p class="text-xs text-gray-500 mt-2">الشعار الحالي موجود</p>
                        @endif
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <x-button type="submit" variant="primary" class="flex-1">
                        <i class="fas fa-save text-xs"></i>
                        <span>حفظ التغييرات</span>
                    </x-button>
                    <x-button type="button" onclick="window.location='{{ route('spa.studios') }}'" variant="secondary" class="flex-1">
                        إلغاء
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
