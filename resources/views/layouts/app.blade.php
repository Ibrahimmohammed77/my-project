<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full bg-gray-50 scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | صوركم</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Fallback to jsdelivr CDN if cloudflare fails -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css" media="print" onload="this.media='all'; this.onload=null;">


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
                    },
                    screens: {
                        'xs': '475px',
                        'sm': '640px',
                        'md': '768px',
                        'lg': '1024px',
                        'xl': '1280px',
                        '2xl': '1536px',
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        let token = document.head.querySelector('meta[name="csrf-token"]');
        if (token) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
        }
    </script>

    <style>
        html {
            scroll-behavior: smooth;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { 
            background: #cbd5e1; 
            border-radius: 10px;
            border: 2px solid transparent;
            background-clip: content-box;
        }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; background-clip: content-box; }

        [x-cloak] { display: none !important; }

        /* Responsive improvements */
        @media (max-width: 640px) {
            /* Improve touch targets on mobile */
            button,
            a,
            [role="button"] {
                min-height: 44px;
                min-width: 44px;
            }
        }

        @media (max-width: 768px) {
            /* Prevent horizontal overflow on mobile */
            img, video, iframe, canvas, svg {
                max-width: 100%;
                height: auto;
            }

            /* Better form elements on mobile */
            input,
            select,
            textarea {
                font-size: 16px !important; /* Prevents iOS zoom on focus */
            }
        }

        /* Tablet adjustments */
        @media (min-width: 769px) and (max-width: 1024px) {
            .tablet\:px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
            }

            a {
                text-decoration: underline !important;
            }
        }

        /* Reduce motion for accessibility */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-gray-800" x-data="{ sidebarOpen: false, isMobile: window.innerWidth < 1024 }" x-init="
    isMobile = window.innerWidth < 1024;
    $watch('sidebarOpen', value => {
        // Prevent body scroll when sidebar is open on mobile/tablet
        if (isMobile) {
            document.body.style.overflow = value ? 'hidden' : '';
        }
    });
    window.addEventListener('resize', () => {
        isMobile = window.innerWidth < 1024;
        if (!isMobile) sidebarOpen = false;
    });
">

    <!-- Mobile Overlay -->
    <template x-if="isMobile">
        <div x-show="sidebarOpen"
             x-transition.opacity
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 transition-all duration-300"
             @click="sidebarOpen = false"
             x-cloak>
        </div>
    </template>

    <div class="flex h-full overflow-hidden">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 bg-gray-50 relative overflow-hidden">
            <!-- Header -->
            <x-header />

            <!-- Content Area - Responsive padding -->
            <div class="flex-1 overflow-y-auto px-3 xs:px-4 sm:px-5 md:px-6 lg:px-8 py-4 sm:py-6 lg:py-8 scroll-smooth">
                <!-- Responsive container for content -->
                <div class="max-w-full sm:max-w-7xl mx-auto w-full">
                    @yield('content')
                </div>

                <!-- Mobile bottom safe area for iOS -->
                <div class="h-8 sm:h-0"></div>
            </div>
        </main>
    </div>



    @stack('scripts')

    <script>
        // Responsive image handling
        document.addEventListener('DOMContentLoaded', function() {
            // Lazy loading for images if needed
            const lazyImages = document.querySelectorAll('img[data-src]');

            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            if (img.dataset.srcset) {
                                img.srcset = img.dataset.srcset;
                            }
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                lazyImages.forEach(img => imageObserver.observe(img));
            }

            // Touch device detection
            const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
            if (isTouchDevice) {
                document.documentElement.classList.add('touch');
            } else {
                document.documentElement.classList.add('no-touch');
            }
        });

        // Prevent zoom on double-tap (iOS)
        document.addEventListener('touchstart', function(event) {
            if (event.touches.length > 1) {
                event.preventDefault();
            }
        }, { passive: false });

        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
    </script>
</body>
</html>
