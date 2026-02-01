@extends('layouts.guest')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
<div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/20">
    <h2 class="text-2xl font-bold text-white text-center mb-6">إعادة تعيين كلمة المرور</h2>
    
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-500/20 border border-red-400/30 rounded-xl text-white text-sm">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    
    <form action="{{ route('password.update') }}" method="post" class="space-y-4">
        @csrf
        
        <!-- Token (Hidden) -->
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label class="block text-sm font-semibold text-white mb-2">البريد الإلكتروني</label>
            <input 
                type="email" 
                name="email" 
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="example@domain.com"
                value="{{ old('email', $email) }}"
                readonly
                required
            >
        </div>
        
        <div>
            <label class="block text-sm font-semibold text-white mb-2">كلمة المرور الجديدة</label>
            <input 
                type="password" 
                name="password" 
                class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-blue-200 focus:border-white focus:ring-2 focus:ring-white/20 outline-none transition-all backdrop-blur-sm" 
                placeholder="••••••••"
                minlength="6"
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
                minlength="6"
                required
            >
        </div>
        
        <button 
            type="submit" 
            class="w-full bg-white hover:bg-blue-50 text-primary px-6 py-3 rounded-xl font-bold shadow-lg transition-all active:scale-95"
        >
            تحديث كلمة المرور
        </button>
    </form>
</div>

<div class="text-center mt-6">
    <p class="text-blue-100 text-sm">
        لم تستلم الرمز؟ 
        <a href="{{ route('password.request') }}" class="text-white font-semibold hover:underline">إعادة الإرسال</a>
    </p>
</div>
@endsection
