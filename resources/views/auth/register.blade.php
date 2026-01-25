@extends('layouts.guest')

@section('title', 'إنشاء حساب جديد')

@section('content')
<div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/20">
    <h2 class="text-2xl font-bold text-white text-center mb-6">إنشاء حساب جديد</h2>
    
    <form action="{{ route('register') }}" method="post" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">الاسم الكامل</label>
            <input 
                type="text" 
                name="name" 
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="أدخل اسمك الكامل"
                required
            >
        </div>
        
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
            <label class="block text-sm font-semibold text-white mb-2">كلمة المرور</label>
            <input 
                type="password" 
                name="password" 
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="••••••••"
                required
            >
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">تأكيد كلمة المرور</label>
            <input 
                type="password" 
                name="password_confirmation" 
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="••••••••"
                required
            >
        </div>
        
        <div class="flex items-start">
            <input 
                type="checkbox" 
                name="terms" 
                id="terms" 
                class="w-4 h-4 mt-1 rounded border-white/20 bg-white/10 text-accent focus:ring-2 focus:ring-white/20"
                required
            >
            <label for="terms" class="mr-2 text-sm text-blue-100">
                أوافق على <a href="#" class="text-white font-semibold hover:underline">الشروط والأحكام</a>
            </label>
        </div>
        
        <button 
            type="submit" 
            class="w-full bg-white hover:bg-blue-50 text-primary px-6 py-3 rounded-xl font-bold shadow-lg transition-all active:scale-95"
        >
            إنشاء الحساب
        </button>
    </form>
</div>

<div class="text-center mt-6">
    <p class="text-blue-100 text-sm">
        لديك حساب بالفعل؟ 
        <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">تسجيل الدخول</a>
    </p>
</div>
@endsection
