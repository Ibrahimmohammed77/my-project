@extends('layouts.app')

@section('title', 'تغيير كلمة المرور')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header -->
        <div class="px-8 py-6 border-b border-gray-50 bg-gray-50/50 flex items-center gap-4">
            <div class="w-12 h-12 bg-accent/10 rounded-2xl flex items-center justify-center text-accent shadow-sm">
                <i class="fas fa-lock-open text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">تغيير كلمة المرور</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">قم بتحديث كلمة المرور الخاصة بك لتأمين حسابك</p>
            </div>
        </div>

        <form action="{{ route('password.change') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 flex items-center gap-3">
                    <i class="fas fa-check-circle text-lg"></i>
                    <span class="font-bold text-sm">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 flex items-center gap-3">
                    <i class="fas fa-times-circle text-lg"></i>
                    <span class="font-bold text-sm">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 text-red-700 rounded-xl border border-red-100">
                    <ul class="list-disc list-inside space-y-1 text-sm font-bold">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Current Password -->
            <div class="space-y-2">
                <label for="current_password" class="block text-sm font-bold text-gray-700">كلمة المرور الحالية</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-accent transition-colors">
                        <i class="fas fa-key"></i>
                    </div>
                    <input type="password" name="current_password" id="current_password" required
                        class="block w-full rounded-xl border border-gray-200 bg-gray-50 py-3.5 pr-11 pl-4 text-gray-900 shadow-sm focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all duration-200 outline-none hover:bg-white focus:bg-white"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="h-px bg-gray-100 my-6"></div>

            <!-- New Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-bold text-gray-700">كلمة المرور الجديدة</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-accent transition-colors">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                            class="block w-full rounded-xl border border-gray-200 bg-gray-50 py-3.5 pr-11 pl-4 text-gray-900 shadow-sm focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all duration-200 outline-none hover:bg-white focus:bg-white"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-bold text-gray-700">تأكيد كلمة المرور</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-accent transition-colors">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="block w-full rounded-xl border border-gray-200 bg-gray-50 py-3.5 pr-11 pl-4 text-gray-900 shadow-sm focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all duration-200 outline-none hover:bg-white focus:bg-white"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <!-- Hint -->
            <div class="bg-blue-50 rounded-xl p-4 flex gap-3 text-blue-700 text-xs font-bold leading-relaxed">
                <i class="fas fa-info-circle text-lg mt-0.5"></i>
                <p>يجب أن تكون كلمة المرور الجديدة مكونة من 8 أحرف على الأقل، وأن تكون مختلفة عن كلمة المرور الحالية.</p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <button type="button" onclick="history.back()" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all">
                    إلغاء
                </button>
                <button type="submit" class="px-8 py-3 rounded-xl bg-accent hover:bg-accent-hover text-white text-sm font-bold shadow-lg shadow-accent/20 hover:shadow-accent/40 hover:-translate-y-0.5 transition-all active:scale-[0.98]">
                    حفظ كلمة المرور
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
