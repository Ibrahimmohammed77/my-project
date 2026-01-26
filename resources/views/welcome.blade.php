<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>صورك - نظام إدارة الصور الاحترافي</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#1D2B45', light: '#2d3f5f', dark: '#0d1621' },
                        accent: { DEFAULT: '#3b82f6', hover: '#2563eb', light: '#60a5fa' }
                    },
                    fontFamily: { sans: ['Cairo', 'sans-serif'] }
                }
            }
        }
    </script>
    
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen font-sans antialiased">
    
    <!-- Header -->
    <header class="absolute top-0 left-0 right-0 z-50">
        <nav class="container mx-auto px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-accent to-accent-hover rounded-2xl flex items-center justify-center shadow-lg">
                    <i class="fa-solid fa-camera-retro text-white text-xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-primary">صورك</h1>
            </div>
            
            @if (Route::has('login'))
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-primary text-white rounded-xl font-bold hover:bg-primary-light transition-all shadow-lg hover:shadow-xl">
                            <i class="fa-solid fa-gauge mr-2"></i>
                            لوحة القيادة
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-2.5 text-primary hover:text-accent font-bold transition-colors">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>
                            تسجيل الدخول
                        </a>
                        
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-accent text-white rounded-xl font-bold hover:bg-accent-hover transition-all shadow-lg hover:shadow-xl">
                                <i class="fa-solid fa-user-plus mr-2"></i>
                                إنشاء حساب
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="container mx-auto px-6 pt-32 pb-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Content -->
            <div class="space-y-6">
                <div class="inline-block px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-bold mb-4">
                    <i class="fa-solid fa-sparkles mr-2"></i>
                    نظام إدارة صور احترافي
                </div>
                
                <h2 class="text-5xl lg:text-6xl font-bold text-primary leading-tight">
                    احفظ ذكرياتك
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-accent to-accent-hover">بطريقة احترافية</span>
                </h2>
                
                <p class="text-xl text-gray-600 leading-relaxed">
                    نظام متكامل لإدارة الصور والفعاليات مع تقنية QR Code، مصمم خصيصاً للمصورين المحترفين ومنظمي الفعاليات.
                </p>
                
                <div class="flex flex-wrap gap-4 pt-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-primary text-white rounded-2xl font-bold hover:bg-primary-light transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 transform">
                            <i class="fa-solid fa-rocket mr-2"></i>
                            ابدأ الآن
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-accent text-white rounded-2xl font-bold hover:bg-accent-hover transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 transform">
                            <i class="fa-solid fa-rocket mr-2"></i>
                            ابدأ مجاناً
                        </a>
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-white text-primary rounded-2xl font-bold hover:bg-gray-50 transition-all shadow-lg border-2 border-gray-200">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>
                            تسجيل الدخول
                        </a>
                    @endauth
                </div>
            </div>
            
            <!-- Image/Illustration -->
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-br from-accent/20 to-accent-hover/20 rounded-3xl blur-3xl"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl p-8 border border-gray-100">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="aspect-square bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-image text-6xl text-accent"></i>
                        </div>
                        <div class="aspect-square bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-qrcode text-6xl text-purple-600"></i>
                        </div>
                        <div class="aspect-square bg-gradient-to-br from-orange-100 to-orange-200 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-users text-6xl text-orange-600"></i>
                        </div>
                        <div class="aspect-square bg-gradient-to-br from-teal-100 to-teal-200 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-chart-line text-6xl text-teal-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="container mx-auto px-6 py-20">
        <div class="text-center mb-16">
            <h3 class="text-4xl font-bold text-primary mb-4">المميزات الرئيسية</h3>
            <p class="text-xl text-gray-600">كل ما تحتاجه لإدارة صورك بشكل احترافي</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-accent to-accent-hover rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fa-solid fa-qrcode text-white text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-primary mb-3">تقنية QR Code</h4>
                <p class="text-gray-600 leading-relaxed">
                    نظام متطور لإنشاء وإدارة رموز QR للوصول السريع للصور والفعاليات
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fa-solid fa-users-gear text-white text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-primary mb-3">إدارة الصلاحيات</h4>
                <p class="text-gray-600 leading-relaxed">
                    نظام متكامل للأدوار والصلاحيات للتحكم الكامل في الوصول
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fa-solid fa-chart-line text-white text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-primary mb-3">تقارير تفصيلية</h4>
                <p class="text-gray-600 leading-relaxed">
                    احصائيات ورسوم بيانية شاملة لمتابعة أداء فعالياتك
                </p>
            </div>
            
            <!-- Feature 4 -->
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fa-solid fa-cloud-arrow-up text-white text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-primary mb-3">رفع سريع</h4>
                <p class="text-gray-600 leading-relaxed">
                    رفع الصور بسرعة وسهولة مع معاينة فورية وتنظيم تلقائي
                </p>
            </div>
            
            <!-- Feature 5 -->
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fa-solid fa-shield-halved text-white text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-primary mb-3">أمان عالي</h4>
                <p class="text-gray-600 leading-relaxed">
                    حماية متقدمة لبياناتك مع نسخ احتياطي تلقائي
                </p>
            </div>
            
            <!-- Feature 6 -->
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all hover:-translate-y-2 border border-gray-100">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                    <i class="fa-solid fa-mobile-screen-button text-white text-2xl"></i>
                </div>
                <h4 class="text-2xl font-bold text-primary mb-3">متجاوب تماماً</h4>
                <p class="text-gray-600 leading-relaxed">
                    واجهة سلسة تعمل بكفاءة على جميع الأجهزة
                </p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="container mx-auto px-6 py-20">
        <div class="bg-gradient-to-br from-primary to-primary-light rounded-3xl p-12 lg:p-16 text-center text-white shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <i class="fa-solid fa-camera-retro absolute -top-10 -right-10 text-9xl transform rotate-12"></i>
                <i class="fa-solid fa-image absolute -bottom-10 -left-10 text-9xl transform -rotate-12"></i>
            </div>
            
            <div class="relative z-10 max-w-3xl mx-auto">
                <h3 class="text-4xl lg:text-5xl font-bold mb-6">جاهز للبدء؟</h3>
                <p class="text-xl text-blue-100 mb-8 leading-relaxed">
                    انضم إلى آلاف المصورين الذين يستخدمون نظامنا لإدارة صورهم بشكل احترافي
                </p>
                
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-block px-10 py-4 bg-white text-primary rounded-2xl font-bold hover:bg-gray-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 transform">
                        <i class="fa-solid fa-gauge mr-2"></i>
                        انتقل إلى لوحة القيادة
                    </a>
                @else
                    <div class="flex flex-wrap gap-4 justify-center">
                        <a href="{{ route('register') }}" class="px-10 py-4 bg-white text-primary rounded-2xl font-bold hover:bg-gray-50 transition-all shadow-xl hover:shadow-2xl hover:-translate-y-1 transform">
                            <i class="fa-solid fa-rocket mr-2"></i>
                            ابدأ الآن مجاناً
                        </a>
                        <a href="{{ route('login') }}" class="px-10 py-4 bg-white/10 backdrop-blur-sm text-white rounded-2xl font-bold hover:bg-white/20 transition-all border-2 border-white/30">
                            <i class="fa-solid fa-right-to-bracket mr-2"></i>
                            تسجيل الدخول
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="container mx-auto px-6 py-12 border-t border-gray-200">
        <div class="text-center text-gray-600">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-accent to-accent-hover rounded-xl flex items-center justify-center">
                    <i class="fa-solid fa-camera-retro text-white"></i>
                </div>
                <span class="text-xl font-bold text-primary">صورك</span>
            </div>
            <p class="text-sm">
                © {{ date('Y') }} صورك - نظام إدارة الصور الاحترافي. جميع الحقوق محفوظة.
            </p>
        </div>
    </footer>

</body>
</html>
