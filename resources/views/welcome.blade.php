<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صوركم - منصة حفظ الذكريات</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animations -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#1D2B45', light: '#2A3C5E', dark: '#0F172A' },
                        accent: { DEFAULT: '#3B82F6', hover: '#2563EB', light: '#60A5FA' },
                        surface: { light: '#F8FAFC', dark: '#1E293B' }
                    },
                    fontFamily: { sans: ['Cairo', 'sans-serif'] },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                        'blob': 'blob 7s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .text-gradient {
            background: linear-gradient(135deg, #1D2B45 0%, #3B82F6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased overflow-x-hidden selection:bg-accent selection:text-white">

    <!-- Background Elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-200/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 right-80 w-[500px] h-[500px] bg-indigo-200/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-20 w-[600px] h-[600px] bg-purple-200/30 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <!-- Navigation -->
    <nav class="fixed w-full z-50 transition-all duration-300" 
         :class="{ 'glass-nav py-3 shadow-sm': scrolled, 'bg-transparent py-5': !scrolled }"
         x-data="{ scrolled: false, mobileOpen: false }"
         @scroll.window="scrolled = (window.pageYOffset > 20)">
        <div class="container mx-auto px-6 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="h-20 w-20 rounded-full overflow-hidden border-2 border-white shadow-lg">
                    <img src="{{ asset('images/logo-new.jpg') }}" alt="صوركم" class="h-full w-full object-cover">
                </div>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-sm font-medium text-gray-600 hover:text-accent transition-colors">المميزات</a>
                <a href="#how-it-works" class="text-sm font-medium text-gray-600 hover:text-accent transition-colors">كيف يعمل؟</a>
                
                @if (Route::has('login'))
                    <div class="flex items-center gap-3 mr-4 border-r border-gray-200 pr-6">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 transition-all duration-300">
                                <i class="fa-solid fa-gauge ml-2"></i>لوحة التحكم
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-bold text-gray-600 hover:text-primary transition-colors">تسجيل الدخول</a>
                            
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-accent text-white text-sm font-bold rounded-xl shadow-lg shadow-accent/20 hover:shadow-accent/40 hover:-translate-y-0.5 transition-all duration-300">
                                    ابدأ الآن
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-700 text-xl focus:outline-none">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
        
        <!-- Mobile Dropdown -->
        <div x-show="mobileOpen" x-transition class="md:hidden absolute top-full left-0 w-full bg-white border-b border-gray-100 shadow-xl py-4 px-6 flex flex-col gap-4">
             <a href="#features" class="text-gray-600 font-medium">المميزات</a>
             <a href="#how-it-works" class="text-gray-600 font-medium">كيف يعمل؟</a>
             <hr class="border-gray-100">
             @auth
                <a href="{{ url('/dashboard') }}" class="text-primary font-bold">لوحة التحكم</a>
             @else
                <a href="{{ route('login') }}" class="text-gray-600 font-medium">تسجيل الدخول</a>
                <a href="{{ route('register') }}" class="text-accent font-bold">إنشاء حساب جديد</a>
             @endauth
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Text Content -->
                <div data-aos="fade-left" data-aos-duration="1000">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-100 text-accent text-xs font-bold mb-6">
                        <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                        المنصة الأحدث لإدارة الصور
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-bold leading-tight text-primary mb-6">
                        احفظ ذكرياتك <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-l from-accent to-purple-600 relative">
                            بذكاء وأمان
                            <svg class="absolute w-full h-3 -bottom-1 left-0 text-accent opacity-30" viewBox="0 0 200 9" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.00025 6.99997C25.7951 2.37257 66.8228 -1.25997 107.031 3.2384C140.231 6.95345 158.349 9.38072 198.001 3.52554" stroke="currentColor" stroke-width="3"/></svg>
                        </span>
                    </h1>
                    
                    <p class="text-lg text-gray-500 mb-8 leading-relaxed max-w-lg">
                        منصة سحابية متكاملة تتيح لك تخزين، تنظيم، ومشاركة صوركم مع العائلة والأصدقاء بأعلى جودة، مع أدوات ذكية للبحث والتعرف على الوجوه.
                    </p>
                    
                    <div class="flex flex-wrap items-center gap-4">
                        @auth
                             <a href="{{ url('/dashboard') }}" class="group relative px-8 py-4 bg-primary text-white rounded-2xl font-bold shadow-xl overflow-hidden transition-all hover:-translate-y-1">
                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                                <span class="relative flex items-center gap-2">
                                    انتقل للوحة التحكم <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                                </span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-primary text-white rounded-2xl font-bold shadow-xl overflow-hidden transition-all hover:-translate-y-1">
                                <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                                <span class="relative flex items-center gap-2">
                                    ابدأ تجربتك المجانية <i class="fa-solid fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                                </span>
                            </a>
                            <a href="#demos" class="px-8 py-4 bg-white text-gray-700 rounded-2xl font-bold border border-gray-200 hover:border-accent hover:text-accent transition-all hover:bg-blue-50">
                                مشاهدة العرض
                            </a>
                        @endauth
                    </div>
                    
                    <div class="mt-10 flex items-center gap-6 text-sm text-gray-400 font-medium">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-green-500"></i> تخزين آمن
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-green-500"></i> جودة عالية
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-green-500"></i> مشاركة سهلة
                        </div>
                    </div>
                </div>
                
                <!-- Visual -->
                <div class="relative" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="200">
                    <div class="relative z-10 grid grid-cols-2 gap-4 max-w-lg mx-auto transform rotate-[-5deg] hover:rotate-0 transition-transform duration-700">
                        <div class="space-y-4 pt-12">
                            <div class="h-48 rounded-2xl bg-cover bg-center shadow-2xl animate-float" style="background-image: url('https://images.unsplash.com/photo-1516035069371-29a1b244cc32?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');"></div>
                            <div class="h-32 rounded-2xl bg-cover bg-center shadow-2xl animate-float-delayed" style="background-image: url('https://images.unsplash.com/photo-1520390138845-fd2d229dd552?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');"></div>
                        </div>
                        <div class="space-y-4">
                            <div class="h-32 rounded-2xl bg-cover bg-center shadow-2xl animate-float-delayed" style="background-image: url('https://images.unsplash.com/photo-1542038784456-1ea8e935640e?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');"></div>
                            <div class="h-48 rounded-2xl bg-cover bg-center shadow-2xl animate-float" style="background-image: url('https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');"></div>
                        </div>
                        
                        <!-- Floating Badges -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white/90 backdrop-blur-md px-6 py-3 rounded-2xl shadow-xl flex items-center gap-3 animate-bounce" style="animation-duration: 3s;">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-accent">
                                <i class="fa-solid fa-cloud-upload-alt text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-bold">تم الرفع</p>
                                <p class="text-sm font-bold text-gray-800">12,500 صورة</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Glow behind -->
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-gradient-to-tr from-accent/30 to-purple-500/30 blur-3xl rounded-full -z-10"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-20" data-aos="fade-up">
                <span class="text-accent font-bold tracking-wider text-sm uppercase">مميزات استثنائية</span>
                <h2 class="text-3xl lg:text-4xl font-bold text-primary mt-2 mb-4">كل ما تحتاجه لإدارة صوركم</h2>
                <p class="text-gray-500">نقدم لك مجموعة متكاملة من الأدوات التي تجعل من تنظيم ومشاركة صوركم تجربة ممتعة وسهلة.</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="glass-card p-8 rounded-3xl hover:shadow-xl transition-all duration-300 group hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-accent mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-bolt text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">سرعة فائقة</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">تصفح ورفع الصور بسرعة البرق بفضل تقنيات التحسين السحابية المتقدمة التي نستخدمها.</p>
                </div>

                <!-- Feature 2 -->
                <div class="glass-card p-8 rounded-3xl hover:shadow-xl transition-all duration-300 group hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center text-purple-600 mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-shield-halved text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">أمان تام</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">نظام تشفير متكامل يضمن خصوصية صوركم وعدم وصول أي شخص غير مصرح له إليها.</p>
                </div>

                <!-- Feature 3 -->
                <div class="glass-card p-8 rounded-3xl hover:shadow-xl transition-all duration-300 group hover:-translate-y-2" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center text-green-600 mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-wand-magic-sparkles text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">تنظيم ذكي</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">صنف صوركم تلقائياً باستخدام الذكاء الاصطناعي حسب الأشخاص، الأماكن، والمناسبات.</p>
                </div>

                <!-- Feature 4 -->
                <div class="glass-card p-8 rounded-3xl hover:shadow-xl transition-all duration-300 group hover:-translate-y-2" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center text-orange-600 mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-share-nodes text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">مشاركة سهلة</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">شارك ألبومات كاملة أو صوراً محددة مع من تحب عبر روابط خاصة ومحمية.</p>
                </div>

                <!-- Feature 5 -->
                <div class="glass-card p-8 rounded-3xl hover:shadow-xl transition-all duration-300 group hover:-translate-y-2" data-aos="fade-up" data-aos-delay="500">
                    <div class="w-14 h-14 bg-pink-100 rounded-2xl flex items-center justify-center text-pink-600 mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-images text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">معارض مذهلة</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">اعرض صوركم في معارض بتصاميم عصرية وجذابة تبرز جمال لقطاتك.</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="glass-card p-8 rounded-3xl hover:shadow-xl transition-all duration-300 group hover:-translate-y-2" data-aos="fade-up" data-aos-delay="600">
                    <div class="w-14 h-14 bg-teal-100 rounded-2xl flex items-center justify-center text-teal-600 mb-6 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-globe text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">وصول دائم</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">تطبيق متوافق مع جميع الأجهزة يتيح لك الوصول لذكرياتك في أي وقت ومن أي مكان.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-primary text-white relative overflow-hidden">
        <div class="absolute inset-0">
             <div class="absolute inset-0 bg-primary opacity-90"></div>
             <!-- Pattern -->
             <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
        </div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-white/10 divide-x-reverse">
                <div data-aos="zoom-in">
                    <div class="text-4xl md:text-5xl font-bold mb-2">10k+</div>
                    <div class="text-blue-200 text-sm">مستخدم نشط</div>
                </div>
                <div data-aos="zoom-in" data-aos-delay="100">
                    <div class="text-4xl md:text-5xl font-bold mb-2">5M+</div>
                    <div class="text-blue-200 text-sm">صورة مرفوعة</div>
                </div>
                <div data-aos="zoom-in" data-aos-delay="200">
                    <div class="text-4xl md:text-5xl font-bold mb-2">99%</div>
                    <div class="text-blue-200 text-sm">نسبة الأمان</div>
                </div>
                <div data-aos="zoom-in" data-aos-delay="300">
                    <div class="text-4xl md:text-5xl font-bold mb-2">24/7</div>
                    <div class="text-blue-200 text-sm">دعم فني</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call To Action -->
    <section class="py-24 relative overflow-hidden">
        <div class="container mx-auto px-6">
            <div class="bg-gradient-to-r from-accent to-blue-600 rounded-[3rem] p-12 lg:p-24 text-center text-white relative overflow-hidden shadow-2xl" data-aos="fade-up">
                
                <!-- Decorations -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full blur-3xl transform -translate-x-1/2 translate-y-1/2"></div>
                
                <div class="relative z-10 max-w-3xl mx-auto">
                    <h2 class="text-4xl lg:text-5xl font-bold mb-8">هل أنت مستعد لحفظ ذكرياتك؟</h2>
                    <p class="text-xl text-blue-100 mb-10 leading-relaxed">
                        انضم اليوم إلى آلاف المستخدمين وابدأ رحلتك في تنظيم وحفظ أجمل لحظات حياتك بأمان وخصوصية تامة.
                    </p>
                    
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-block px-12 py-5 bg-white text-accent rounded-2xl font-bold text-lg hover:bg-gray-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 transform">
                            الذهاب للوحة التحكم
                        </a>
                    @else
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="{{ route('register') }}" class="w-full sm:w-auto px-12 py-5 bg-white text-accent rounded-2xl font-bold text-lg hover:bg-gray-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 transform">
                                إنشاء حساب مجاني
                            </a>
                            <a href="{{ route('login') }}" class="w-full sm:w-auto px-12 py-5 bg-transparent border-2 border-white/30 text-white rounded-2xl font-bold text-lg hover:bg-white/10 transition-all">
                                تسجيل الدخول
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white pt-20 pb-10 border-t border-gray-100">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-24 w-24 rounded-full overflow-hidden border-2 border-gray-100 shadow-md">
                            <img src="{{ asset('images/logo-new.jpg') }}" alt="صوركم" class="h-full w-full object-cover">
                        </div>
                    </div>
                    <p class="text-gray-500 leading-relaxed max-w-md">
                        منصة صوركم هي وجهتك الأولى لحفظ ومشاركة ذكرياتك الثمينة. نحن نؤمن بأن كل لحظة تستحق أن تخلد بأعلى جودة وأمان.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bold text-primary text-lg mb-6">روابط سريعة</h4>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-500 hover:text-accent transition-colors">عن المنصة</a></li>
                        <li><a href="#features" class="text-gray-500 hover:text-accent transition-colors">المميزات</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-accent transition-colors">الأسعار</a></li>
                        <li><a href="#" class="text-gray-500 hover:text-accent transition-colors">الشروط والأحكام</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-bold text-primary text-lg mb-6">تواصل معنا</h4>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-gray-500">
                            <i class="fa-solid fa-envelope text-accent"></i>
                            <span dir="ltr" class="font-sans">info@soarak.com</span>
                        </li>
                        <li class="flex items-center gap-3 text-gray-500">
                            <i class="fa-solid fa-phone text-accent"></i>
                            <span dir="ltr" class="font-sans font-bold text-lg">+967 770 000 000</span>
                        </li>
                    </ul>
                    <div class="flex items-center gap-4 mt-8">
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-accent hover:text-white transition-all">
                            <i class="fa-brands fa-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-accent hover:text-white transition-all">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-accent hover:text-white transition-all">
                            <i class="fa-brands fa-linkedin hover:bg-white"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-100 pt-8 text-center">
                <p class="text-gray-400 text-sm">© {{ date('Y') }} جميع الحقوق محفوظة لمنصة صوركم</p>
            </div>
        </div>
    </footer>

    <!-- Init AOS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            offset: 50,
            duration: 800,
            easing: 'ease-out-cubic',
        });
    </script>
</body>
</html>
