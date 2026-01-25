<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'صورك') | لوحة القيادة</title>
    
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
                        primary: {
                            DEFAULT: '#1D2B45',
                            light: '#2d3f5f',
                            dark: '#0d1621'
                        },
                        accent: {
                            DEFAULT: '#3b82f6',
                            hover: '#2563eb',
                            soft: '#eff6ff'
                        },
                        surface: {
                            DEFAULT: '#ffffff',
                            alt: '#f8fafc'
                        }
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
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Smooth Transitions */
        .transition-all-300 { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Glass Effect Helper */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-[#f1f5f9] text-gray-800 font-sans antialiased overflow-hidden h-screen flex">

    <div id="backdrop" class="fixed inset-0 bg-primary-dark/50 z-20 hidden lg:hidden transition-opacity opacity-0" onclick="toggleSidebar()"></div>

    @include('layouts.partials.sidebar')

    <main class="flex-1 flex flex-col h-full overflow-hidden relative w-full">
        
        @include('layouts.partials.header')

        <div class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-6 lg:p-8 scroll-smooth">
            @yield('content')

            <footer class="mt-10 text-center text-gray-400 text-xs py-4">
                <p>&copy; {{ date('Y') }} صورك. جميع الحقوق محفوظة. <span class="mx-1">|</span> تصميم وتطوير <span class="font-bold text-gray-500">إبراهيم الشامي</span></p>
            </footer>
        </div>
    </main>

    <script>
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('backdrop');

        function toggleSidebar() {
            if (sidebar.classList.contains('translate-x-full')) {
                sidebar.classList.remove('translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                }, 10);
            } else {
                sidebar.classList.add('translate-x-full');
                backdrop.classList.add('opacity-0');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>
