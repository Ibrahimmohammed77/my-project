<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | صورك</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
        
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.baseURL = '/api'; // Global API Base URL
        
        let token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
    </script>

    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-track { background: transparent; }

        /* Original Sidebar Styles */
        .sidebar-gradient {
            background: linear-gradient(180deg, #1D2B45 0%, #0d1621 100%);
        }
        
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(to bottom, #3b82f6, #60a5fa);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .nav-link:hover::before,
        .nav-link.active::before {
            transform: scaleY(1);
        }
        
        .nav-link.active {
            background: linear-gradient(to left, rgba(59, 130, 246, 0.15), transparent);
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }
        
        .nav-link:hover .icon-wrapper {
            background: rgba(59, 130, 246, 0.2);
            transform: scale(1.1);
        }
        
        .nav-link.active .icon-wrapper {
            background: rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body class="bg-[#F3F4F6] font-sans antialiased text-gray-800" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div 
        x-show="sidebarOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false" 
        class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"
        style="display: none;"
    ></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#F3F4F6]">
            
            <!-- Global Header -->
            <x-header />

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-4 lg:p-8">
                <div class="max-w-7xl mx-auto space-y-6">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
