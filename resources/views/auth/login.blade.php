@extends('layouts.guest')

@section('title', 'تسجيل الدخول')

@section('content')
<div class="min-h-screen flex">
    
    <!-- Right Side: Login Form -->
    <div class="flex-1 flex items-center justify-center p-4 sm:p-12 lg:p-20 bg-white relative z-10">
        <div class="w-full max-w-md space-y-8">
            <div class="text-center md:text-right">
                <a href="/" class="inline-block md:hidden mb-6">
                    <img src="{{ asset('images/logo-cutout.png') }}" class="h-16 w-auto" alt="صوركم">
                </a>
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">مرحباً بعودتك! 👋</h2>
                <p class="mt-2 text-sm text-gray-600">
                    الرجاء إدخال بياناتك لتسجيل الدخول
                </p>
            </div>

            <!-- Error Message -->
            <div id="error-message" class="hidden rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-red-400"></i>
                    </div>
                    <div class="mr-3">
                        <h3 class="text-sm font-medium text-red-800">حدث خطأ في تسجيل الدخول</h3>
                        <div class="mt-2 text-sm text-red-700 error-text"></div>
                    </div>
                </div>
            </div>

            <form id="login-form" class="mt-8 space-y-6">
                <div class="space-y-5">
                    <div>
                        <label for="login" class="block text-sm font-bold text-gray-700 mb-2">اسم المستخدم / البريد / الهاتف</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                <i class="fa-regular fa-envelope"></i>
                            </div>
                            <input 
                                id="login" 
                                name="login" 
                                type="text" 
                                required 
                                class="block w-full rounded-xl border border-gray-200 bg-gray-50 py-3.5 pr-11 pl-4 text-gray-900 placeholder:text-gray-400 focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/20 sm:text-sm sm:leading-6 transition-all duration-200 outline-none" 
                                placeholder="مثال: admin@soarak.com"
                            >
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                             <label for="password" class="block text-sm font-bold text-gray-700">كلمة المرور</label>
                             <a href="{{ route('password.request') }}" class="text-sm font-medium text-accent hover:text-accent-hover">نسيت كلمة المرور؟</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                class="block w-full rounded-xl border border-gray-200 bg-gray-50 py-3.5 pr-11 pl-4 text-gray-900 placeholder:text-gray-400 focus:bg-white focus:border-accent focus:ring-2 focus:ring-accent/20 sm:text-sm sm:leading-6 transition-all duration-200 outline-none" 
                                placeholder="••••••••"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-accent focus:ring-accent">
                        <label for="remember" class="mr-2 block text-sm text-gray-700">تذكرني على هذا الجهاز</label>
                    </div>
                </div>

                <button type="submit" id="submit-btn" class="flex w-full justify-center items-center rounded-xl bg-primary px-3 py-4 text-sm font-bold text-white shadow-lg hover:bg-primary-light hover:shadow-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all duration-200 transform active:scale-[0.98]">
                    <span>تسجيل الدخول</span>
                    <i class="fa-solid fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                </button>
                
                <p class="mt-2 text-center text-sm text-gray-500">
                    ليس لديك حساب؟
                    <a href="{{ route('register') }}" class="font-bold text-accent hover:text-accent-hover transition-colors">إنشاء حساب جديد</a>
                </p>
            </form>
            
            <div class="mt-10 border-t border-gray-100 pt-6">
                <div class="grid grid-cols-2 gap-3">
                    <a href="/" class="flex justify-center items-center gap-2 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-primary transition-all">
                        <i class="fa-solid fa-house"></i> الرئيسية
                    </a>
                    <a href="#" class="flex justify-center items-center gap-2 py-2.5 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-primary transition-all">
                        <i class="fa-solid fa-headset"></i> الدعم الفني
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Left Side: Visual / Background -->
    <div class="hidden lg:block relative flex-1 bg-primary overflow-hidden">
        <!-- Background Image with Overlay -->
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30 mix-blend-overlay" src="{{ asset('images/auth-bg-login.jpg') }}" alt="Background">
            <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary/90 to-accent/80 mix-blend-multiply"></div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-96 h-96 bg-accent/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
        
        <!-- Content -->
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-12 text-white">
            <div class="mb-8 p-6 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl animate-float">
                <img src="{{ asset('images/logo-cutout.png') }}" alt="Logo" class="h-32 w-auto drop-shadow-lg">
            </div>
            
            <h1 class="text-4xl font-bold mb-6">منصة صوركم</h1>
            <p class="text-lg text-blue-100 max-w-md leading-relaxed">
                استمتع بتجربة فريدة في حفظ وإدارة ذكرياتك. نظام متكامل يجمع بين الأمان، السهولة، 
            </p>
            
            <!-- Features Micro-grid -->
            <div class="grid grid-cols-3 gap-6 mt-12 w-full max-w-lg">
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <i class="fa-solid fa-shield-halved text-2xl mb-2 text-accent-light"></i>
                    <div class="text-xs font-bold">حماية عالية</div>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <i class="fa-solid fa-bolt text-2xl mb-2 text-yellow-400"></i>
                    <div class="text-xs font-bold">سرعة خارقة</div>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <i class="fa-solid fa-wand-magic-sparkles text-2xl mb-2 text-purple-400"></i>
                    <div class="text-xs font-bold">تنظيم ذكي</div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

const loginForm = document.getElementById('login-form');
const submitBtn = document.getElementById('submit-btn');
const errorMessage = document.getElementById('error-message');
const errorText = errorMessage.querySelector('.error-text');

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Reset state
    submitBtn.disabled = true;
    const btnContent = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
    errorMessage.classList.add('hidden');
    
    const formData = {
        login: document.getElementById('login').value,
        password: document.getElementById('password').value,
        remember: document.getElementById('remember').checked
    };
    
    try {
        const response = await axios.post('{{ route("login.post") }}', formData);
        
        if (response.data.success) {
            submitBtn.innerHTML = '<i class="fa-solid fa-check ml-2"></i> تم بنجاح';
            submitBtn.classList.remove('bg-primary');
            submitBtn.classList.add('bg-green-600');
            
            setTimeout(() => {
                window.location.href = response.data.redirect || '/dashboard';
            }, 500);
        }
    } catch (error) {
        // Show error
        let errorMsg = 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.';
        
        if (error.response?.data?.message) {
            errorMsg = error.response.data.message;
        } else if (error.response?.data?.errors) {
            errorMsg = Object.values(error.response.data.errors).flat().join('<br>');
        }
        
        errorText.innerHTML = errorMsg;
        errorMessage.classList.remove('hidden');
        
        // Reset button
        submitBtn.disabled = false;
        submitBtn.innerHTML = btnContent;
    }
});
</script>
@endpush
@endsection
