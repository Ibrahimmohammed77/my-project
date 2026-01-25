@extends('layouts.guest')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/20">
    <h2 class="text-2xl font-bold text-white text-center mb-6">تسجيل الدخول</h2>
    
    <form action="{{ route('login') }}" method="post" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">البريد الإلكتروني</label>
            <input 
                type="email" 
                name="email" 
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="example@domain.com"
                required
            >
        </div>
        
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-semibold text-white">كلمة المرور</label>
                <a href="#" class="text-xs text-blue-200 hover:text-white transition-colors">نسيت كلمة المرور؟</a>
            </div>
            <input 
                type="password" 
                name="password" 
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="••••••••"
                required
            >
        </div>
        
        <div class="flex items-center">
            <input 
                type="checkbox" 
                name="remember" 
                id="remember" 
                class="w-4 h-4 rounded border-white/20 bg-white/10 text-accent focus:ring-2 focus:ring-white/20"
            >
            <label for="remember" class="mr-2 text-sm text-blue-100">تذكرني على هذا الجهاز</label>
        </div>
        
        <button 
            type="submit" 
            class="w-full bg-white hover:bg-blue-50 text-primary px-6 py-3 rounded-xl font-bold shadow-lg transition-all active:scale-95"
        >
            تسجيل الدخول
        </button>
    </form>
</div>

<div class="text-center mt-6">
    <p class="text-blue-100 text-sm">
        ليس لديك حساب؟ 
        <a href="{{ route('register') }}" class="text-white font-semibold hover:underline">إنشاء حساب جديد</a>
    </p>
</div>
@endsection
