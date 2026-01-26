<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | صوركم</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: '#1D2B45', light: '#2A3C5E', dark: '#0F172A' },
                        accent: { DEFAULT: '#3B82F6', hover: '#2563EB', light: '#60A5FA' },
                        surface: { light: '#FFFFFF', dark: '#1E293B' }
                    },
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                        'card': '0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025)',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        let token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
    </script>

    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-gray-800" x-data="{ sidebarOpen: false }">

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden" @click="sidebarOpen = false"></div>

    <div class="flex h-full">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-50">
            <!-- Header -->
            <x-header />

            <!-- Content Area -->
            <div class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-8 scroll-smooth">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
