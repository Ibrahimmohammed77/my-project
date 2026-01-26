<header class="bg-white border-b border-gray-200 px-6 py-4 shadow-sm sticky top-0 z-30">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-primary transition-colors p-1">
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <div>
                <h2 class="text-2xl font-bold text-gray-800">@yield('title')</h2>
                <p class="text-sm text-gray-500 mt-1">@yield('page-description', 'لوحة التحكم')</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <button class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                <i class="fas fa-bell"></i>
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>
            
            <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
            
            <div class="flex items-center gap-2">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-gray-800">{{ Auth::user()->name ?? 'المستخدم' }}</p>
                    <p class="text-xs text-gray-500">متصل الآن</p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-accent to-blue-400 rounded-full flex items-center justify-center text-white shadow-lg shadow-accent/20">
                    <i class="fas fa-user text-sm"></i>
                </div>
            </div>
        </div>
    </div>
</header>
