@extends('layouts.guest')

@section('title', 'إنشاء حساب جديد')

@section('content')
<div class="min-h-screen flex bg-white">
    
    <!-- Right Side: Register Form -->
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-20 xl:px-24 relative z-10 transition-all duration-500 ease-in-out">
        <div class="mx-auto w-full max-w-lg">
            
            <!-- Mobile Header -->
            <div class="text-center lg:text-right mb-10">
                <a href="/" class="inline-block lg:hidden mb-8 transform hover:scale-105 transition-transform duration-300">
                    <img src="{{ asset('images/logo-cutout.png') }}" class="h-20 w-auto drop-shadow-md" alt="صوركم">
                </a>
                <h2 class="text-4xl font-extrabold tracking-tight text-gray-900 mb-2">انضم إلينا اليوم! </h2>
                <p class="text-lg text-gray-500">
                    أنشئ حسابك الجديد وابدأ في حفظ ذكرياتك
                </p>
            </div>

            <!-- Error Message Container (Animated) -->
            <div id="error-message" class="hidden transform transition-all duration-300 ease-out translate-y-2 opacity-0 mb-6">
                 <div class="rounded-2xl border border-red-100 bg-red-50/80 backdrop-blur-sm p-4 shadow-sm flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-circle-exclamation text-red-500 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-800">تنبيه</h3>
                        <div class="mt-1 text-sm text-red-600 error-text leading-relaxed"></div>
                    </div>
                </div>
            </div>

            <form id="register-form" class="space-y-6">
                
                <!-- Name & Username -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="group">
                        <label for="full_name" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-accent transition-colors">الاسم الكامل</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-accent transition-colors">
                                <i class="fa-regular fa-user"></i>
                            </div>
                            <input id="full_name" name="full_name" type="text" required 
                                class="block w-full rounded-xl border border-gray-200 bg-white py-3.5 pr-11 pl-4 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all duration-200 outline-none hover:border-gray-300"
                                placeholder="الاسم الكامل">
                        </div>
                    </div>

                    <div class="group">
                        <label for="username" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-accent transition-colors">اسم المستخدم</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-accent transition-colors">
                                <i class="fa-solid fa-at"></i>
                            </div>
                            <input id="username" name="username" type="text" required 
                                class="block w-full rounded-xl border border-gray-200 bg-white py-3.5 pr-11 pl-4 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all duration-200 outline-none hover:border-gray-300 text-left" dir="ltr"
                                placeholder="username">
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="group">
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-accent transition-colors">البريد الإلكتروني</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-accent transition-colors">
                             <i class="fa-regular fa-envelope"></i>
                        </div>
                        <input id="email" name="email" type="email" 
                            class="block w-full rounded-xl border border-gray-200 bg-white py-3.5 pr-11 pl-4 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all duration-200 outline-none hover:border-gray-300 text-left" dir="ltr"
                            placeholder="name@example.com">
                    </div>
                </div>

                <div class="group">
                    <label for="phone" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-accent transition-colors">رقم الهاتف</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-accent transition-colors">
                             <i class="fa-solid fa-phone"></i>
                        </div>
                        <input id="phone" name="phone" type="tel" required 
                            class="block w-full rounded-xl border border-gray-200 bg-white py-3.5 pr-11 pl-4 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all duration-200 outline-none hover:border-gray-300 text-left" dir="ltr"
                            placeholder="05xxxxxxxx">
                    </div>
                </div>

                <!-- Security -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="group">
                        <label for="password" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-accent transition-colors">كلمة المرور</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-accent transition-colors">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input id="password" name="password" type="password" required 
                                class="block w-full rounded-xl border border-gray-200 bg-white py-3.5 pr-11 pl-4 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all duration-200 outline-none hover:border-gray-300"
                                placeholder="••••••••">
                        </div>
                    </div>
                    <div class="group">
                        <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2 group-focus-within:text-accent transition-colors">تأكيد كلمة المرور</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400 group-focus-within:text-accent transition-colors">
                                <i class="fa-solid fa-check-double"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required 
                                class="block w-full rounded-xl border border-gray-200 bg-white py-3.5 pr-11 pl-4 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all duration-200 outline-none hover:border-gray-300"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="flex items-center gap-3 p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:bg-white hover:border-gray-200 transition-all cursor-pointer" onclick="document.getElementById('terms').click()">
                    <div class="flex h-6 items-center">
                        <input id="terms" name="terms" type="checkbox" required class="w-5 h-5 rounded border-gray-300 text-accent focus:ring-offset-0 focus:ring-accent transition-all cursor-pointer">
                    </div>
                    <div class="text-sm">
                        <label for="terms" class="font-medium text-gray-700 cursor-pointer select-none">أوافق على <a href="#" class="text-accent hover:text-accent-hover hover:underline">الشروط والأحكام</a> و <a href="#" class="text-accent hover:text-accent-hover hover:underline">سياسة الخصوصية</a></label>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submit-btn" class="group relative flex w-full justify-center items-center rounded-xl bg-gradient-to-l from-primary to-primary-light px-3 py-4 text-sm font-bold text-white shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-all duration-300 transform active:scale-[0.98]">
                    <span class="text-base">إنشاء الحساب</span>
                    <i class="fa-solid fa-arrow-left mr-2 text-lg group-hover:-translate-x-1 transition-transform"></i>
                    
                    <!-- Glow Effect -->
                    <div class="absolute inset-0 rounded-xl ring-2 ring-white/20 group-hover:ring-white/40 transition-all"></div>
                </button>
                
                <p class="mt-4 text-center text-sm text-gray-500 font-medium">
                    لديك حساب بالفعل؟
                    <a href="{{ route('login') }}" class="font-bold text-accent hover:text-accent-hover transition-colors inline-flex items-center gap-1 group">
                        تسجيل الدخول
                        <i class="fa-solid fa-arrow-left text-[10px] opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all"></i>
                    </a>
                </p>
            </form>
            
            <!-- Footer Links -->
            <div class="mt-10 border-t border-gray-100 pt-6">
                <div class="flex justify-center gap-6">
                    <a href="/" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-wider">الرئيسية</a>
                    <a href="#" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-wider">المساعدة</a>
                    <a href="#" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-wider">الخصوصية</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Left Side: Visual / Background -->
    <div class="hidden lg:block relative w-0 flex-1 overflow-hidden">
         <!-- Background Image -->
        <div class="absolute inset-0">
            <img class="h-full w-full object-cover opacity-30 mix-blend-overlay" src="{{ asset('images/auth-bg-login.jpg') }}" alt="Background">
            <div class="absolute inset-0 bg-gradient-to-br from-primary via-primary/90 to-accent/80 mix-blend-multiply"></div>
        </div>
        
        <!-- Content Overlay -->
        <div class="absolute inset-0 flex flex-col items-center justify-center p-12 text-center text-white z-20">
            <div class="bg-white/10 backdrop-blur-md rounded-3xl p-8 border border-white/20 shadow-2xl max-w-lg transform hover:scale-105 transition-all duration-500">
                <img src="{{ asset('images/logo-cutout.png') }}" class="h-32 w-auto mx-auto mb-8 drop-shadow-xl" alt="Logo">
                
                <h1 class="text-4xl font-extrabold mb-4 tracking-tight">ابدأ رحلتك الإبداعية</h1>
                <p class="text-lg text-blue-50 leading-relaxed mb-8 opacity-90">
                    منصة صوركم توفر لك المكان الأمثل لحفظ ذكرياتك بأمان ومشاركتها بذكاء. انضم إلينا واكتشف الفارق.
                </p>
                
                <div class="flex flex-wrap justify-center gap-3">
                    <span class="px-4 py-2 bg-white/20 rounded-full text-xs font-bold border border-white/10 backdrop-blur-sm">🔒 تشفير متقدم</span>
                    <span class="px-4 py-2 bg-white/20 rounded-full text-xs font-bold border border-white/10 backdrop-blur-sm">⚡ سرعة عالية</span>
                    <span class="px-4 py-2 bg-white/20 rounded-full text-xs font-bold border border-white/10 backdrop-blur-sm">💎 جودة أصلية</span>
                </div>
            </div>
            
            <div class="absolute bottom-10 text-xs text-white/60 font-medium tracking-widest uppercase">
                © {{ date('Y') }} Soarak Platform
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @vite('resources/js/auth/pages/register.js')
@endpush
@endsection
