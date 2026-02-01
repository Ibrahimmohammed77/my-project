@extends('layouts.guest')

@section('title', 'نسيت كلمة المرور')

@section('content')
<div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/20">
    <h2 class="text-2xl font-bold text-white text-center mb-6">استعادة كلمة المرور</h2>
    
    @if(session('reset_code_sent'))
        <div class="mb-4 p-4 bg-green-500/20 border border-green-400/30 rounded-xl text-white text-sm">
            <p>✓ تم إرسال رمز التحقق بنجاح!</p>
            @if(session('reset_code_debug'))
                <p class="mt-2 font-mono bg-black/20 p-2 rounded">رمز التحقق (للتطوير): {{ session('reset_code_debug') }}</p>
            @endif
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-500/20 border border-red-400/30 rounded-xl text-white text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    
    <form action="{{ route('password.send-code') }}" method="post" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">البريد الإلكتروني</label>
            <input 
                type="email" 
                name="email" 
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="example@domain.com"
                value="{{ old('email') }}"
                required
            >
            <p class="text-xs text-blue-200 mt-2">أدخل البريد الإلكتروني المسجل في حسابك لاستلام رابط استعادة كلمة المرور</p>
        </div>
        
        <button 
            type="submit" 
            class="w-full bg-white hover:bg-blue-50 text-primary px-6 py-3 rounded-xl font-bold shadow-lg transition-all active:scale-95"
        >
            إرسال رمز التحقق
        </button>
    </form>
</div>

<div class="text-center mt-6">
    <p class="text-blue-100 text-sm">
        تذكرت كلمة المرور؟ 
        <a href="{{ route('login') }}" class="text-white font-semibold hover:underline">تسجيل الدخول</a>
    </p>
</div>
@endsection
