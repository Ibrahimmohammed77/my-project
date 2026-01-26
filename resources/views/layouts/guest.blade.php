<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'تسجيل الدخول') | صورك</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                            alt: '#f8fafc',
                            primary: { DEFAULT: '#1D2B45', light: '#2d3f5f', dark: '#0d1621' },
                            accent: { DEFAULT: '#3b82f6', hover: '#2563eb', light: '#60a5fa' }
                        },
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(26, 38, 57, 0.05)',
                        'glow': '0 0 15px rgba(59, 130, 246, 0.3)'
                    }
                }
            }
        }
    </script>
    
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gradient-to-br from-primary via-primary-light to-accent min-h-screen flex items-center justify-center font-sans antialiased p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-6">
                <img src="{{ asset('images/logo-white.jpg') }}" alt="صورك" class="h-24 w-auto object-contain drop-shadow-xl">
            </div>
            <p class="text-blue-100 text-sm font-medium">نظام إدارة الصور والفعاليات</p>
        </div>
        
        @yield('content')
        
        <div class="text-center mt-8 text-blue-100 text-xs">
            <p>&copy; {{ date('Y') }} صورك. جميع الحقوق محفوظة.</p>
        </div>
    </div>
    
    @stack('scripts')
</body>
</html>
