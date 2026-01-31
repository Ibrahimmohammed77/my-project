@extends('layouts.app')
@section('title', 'تعديل حساب')

@section('content')
    <x-page-header title="تعديل الحساب">
        <x-button onclick="window.location='{{ route('spa.accounts') }}'" variant="secondary">
            <i class="fas fa-arrow-right text-xs"></i>
            <span>العودة</span>
        </x-button>
    </x-page-header>

    <div class="max-w-3xl">
        <div class="bg-white rounded-2xl shadow-card p-8">
            <form id="account-form" action="{{ route('accounts.update', $user->id) }}" method="POST" class="space-y-6">
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
                        label="الاسم الكامل" 
                        icon="fa-id-card" 
                        value="{{ old('full_name', $user->name) }}"
                        required 
                    />
                    
                    <x-form.input 
                        id="username" 
                        name="username" 
                        label="اسم المستخدم" 
                        icon="fa-user" 
                        value="{{ old('username', $user->username) }}"
                        required 
                    />
                    
                    <x-form.input 
                        id="email" 
                        name="email" 
                        type="email" 
                        label="البريد الإلكتروني" 
                        icon="fa-envelope" 
                        value="{{ old('email', $user->email) }}"
                        required 
                    />
                    
                    <x-form.input 
                        id="phone" 
                        name="phone" 
                        label="رقم الهاتف" 
                        icon="fa-phone" 
                        value="{{ old('phone', $user->phone) }}"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Role -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الدور <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="fas fa-user-shield absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                                <select id="role_id" name="role_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->role_id }}" @if($user->roles->contains($role->role_id)) selected @endif>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">حالة الحساب <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="fas fa-toggle-on absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                                <select id="user_status_id" name="user_status_id" class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm appearance-none" required>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->lookup_value_id }}" @if($user->user_status_id == $status->lookup_value_id) selected @endif>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Section (Optional) -->
                <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-1 h-5 bg-orange-500 rounded-full"></span>
                        <h3 class="text-sm font-bold text-gray-700">تغيير كلمة المرور (اختياري)</h3>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <x-form.input 
                            id="password" 
                            name="password" 
                            type="password" 
                            label="كلمة المرور الجديدة" 
                            icon="fa-lock" 
                            placeholder="اتركه فارغاً للاحتفاظ بكلمة المرور الحالية"
                        />
                        <x-form.input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            label="تأكيد كلمة المرور" 
                            icon="fa-lock"
                        />
                    </div>
                </div>

                <div class="flex gap-3 pt-4">
                    <x-button type="submit" variant="primary" class="flex-1">
                        <i class="fas fa-save text-xs"></i>
                        <span>حفظ التغييرات</span>
                    </x-button>
                    <x-button type="button" onclick="window.location='{{ route('spa.accounts') }}'" variant="secondary" class="flex-1">
                        إلغاء
                    </x-button>
                </div>
            </form>
        </div>
    </div>
@endsection
