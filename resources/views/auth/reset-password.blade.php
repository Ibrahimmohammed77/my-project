@extends('layouts.guest')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
<div class="max-w-md w-full mx-auto">
    <div class="bg-white/10 backdrop-blur-xl rounded-[2.5rem] p-8 sm:p-12 shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-white/10 relative overflow-hidden group">
        <!-- Decoration -->
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-accent/10 rounded-full blur-3xl group-hover:bg-accent/20 transition-colors duration-700"></div>
        
        <div class="relative z-10">
            <div class="text-center mb-10">
                <div class="w-20 h-20 bg-accent/20 rounded-3xl flex items-center justify-center mx-auto mb-6 transform group-hover:rotate-12 transition-transform duration-500">
                    <i class="fa-solid fa-lock-open text-3xl text-accent-light"></i>
                </div>
                <h2 class="text-3xl font-black text-white mb-3">تحديث كلمة المرور 🔐</h2>
                <p class="text-blue-100/70 text-base font-medium">قم بتعيين كلمة مرور قوية وجديدة لحسابك</p>
            </div>
            
            @if($errors->any())
                <div class="mb-8 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-100 text-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-400 text-lg"></i>
                    <div class="space-y-1">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <form action="{{ route('password.update') }}" method="post" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="group/input">
                    <label class="block text-sm font-bold text-blue-100 mb-2 mr-1">البريد الإلكتروني</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-blue-200/50">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            class="w-full px-4 py-4 pr-11 rounded-2xl bg-white/5 border border-white/10 text-white/50 placeholder-blue-200/30 outline-none backdrop-blur-md font-medium cursor-not-allowed" 
                            value="{{ $email }}"
                            readonly
                            required
                        >
                    </div>
                </div>

                <div class="group/input">
                    <label class="block text-sm font-bold text-blue-100 mb-2 mr-1">كلمة المرور الجديدة</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-blue-200/50 group-focus-within/input:text-accent transition-colors">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password" 
                            class="w-full px-4 py-4 pr-11 rounded-2xl bg-white/5 border border-white/10 text-white placeholder-blue-200/30 focus:border-accent focus:ring-4 focus:ring-accent/10 outline-none transition-all backdrop-blur-md font-medium" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>

                <div class="group/input">
                    <label class="block text-sm font-bold text-blue-100 mb-2 mr-1">تأكيد كلمة المرور</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-blue-200/50 group-focus-within/input:text-accent transition-colors">
                            <i class="fa-solid fa-check-double"></i>
                        </div>
                        <input 
                            type="password" 
                            name="password_confirmation" 
                            class="w-full px-4 py-4 pr-11 rounded-2xl bg-white/5 border border-white/10 text-white placeholder-blue-200/30 focus:border-accent focus:ring-4 focus:ring-accent/10 outline-none transition-all backdrop-blur-md font-medium" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>
                
                <button 
                    type="submit" 
                    class="w-full bg-accent hover:bg-accent-hover text-white px-6 py-4 rounded-2xl font-black shadow-lg shadow-accent/20 transition-all transform active:scale-[0.98] flex items-center justify-center gap-3 group/btn"
                >
                    <span>تحديث كلمة المرور</span>
                    <i class="fa-solid fa-shield-check text-sm group-hover:scale-110 transition-transform"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<div class="text-center mt-6">
    <p class="text-blue-100 text-sm">
        لم تستلم الرمز؟ 
        <a href="{{ route('password.request') }}" class="text-white font-semibold hover:underline">إعادة الإرسال</a>
    </p>
</div>
@endsection
