<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | صورك</title>
    
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
                        accent: { DEFAULT: '#3b82f6', hover: '#2563eb' }
                    },
                    fontFamily: { sans: ['Cairo', 'sans-serif'] }
                }
            }
        }
    </script>
    
    <style>
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-primary text-white flex-shrink-0">
            <div class="p-6 border-b border-white/10">
                <h1 class="text-2xl font-bold">صورك</h1>
                <p class="text-xs text-blue-200 mt-1">لوحة التحكم</p>
            </div>
            
            <nav class="p-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition">
                    <i class="fas fa-home"></i>
                    <span>الرئيسية</span>
                </a>
                <a href="{{ route('spa.accounts') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition">
                    <i class="fas fa-users"></i>
                    <span>الحسابات</span>
                </a>
                <a href="{{ route('spa.roles') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition">
                    <i class="fas fa-user-shield"></i>
                    <span>الأدوار</span>
                </a>
                <a href="{{ route('spa.permissions') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-white/10 transition">
                    <i class="fas fa-key"></i>
                    <span>الصلاحيات</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-800">@yield('page-title')</h2>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600" id="user-name">المستخدم</span>
                        <button onclick="logout()" class="text-red-600 hover:text-red-700">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Setup Axios
        axios.defaults.baseURL = '/api';
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.headers.common['Accept'] = 'application/json';
        
        const token = localStorage.getItem('auth_token');
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }

        // Logout function
        function logout() {
            if (confirm('هل تريد تسجيل الخروج؟')) {
                axios.post('/auth/logout').finally(() => {
                    localStorage.removeItem('auth_token');
                    window.location.href = '/login';
                });
            }
        }

        // Load user info
        axios.get('/auth/me').then(res => {
            document.getElementById('user-name').textContent = res.data.data.account.full_name;
        }).catch(() => {
            window.location.href = '/login';
        });
    </script>
    
    @stack('scripts')
</body>
</html>
