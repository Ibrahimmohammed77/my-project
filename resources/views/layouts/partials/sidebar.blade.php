<aside id="sidebar" class="fixed lg:static inset-y-0 right-0 z-30 w-[280px] bg-primary text-white transform translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl lg:shadow-none">
    <div class="h-20 flex items-center px-8 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="relative flex items-center justify-center w-12 h-12 bg-white rounded-xl shadow-lg">
                <i class="fa-solid fa-camera text-2xl text-primary"></i>
            </div>
            <div class="flex flex-col">
                <span class="text-2xl font-bold tracking-wide leading-none text-white">صوركم</span>
                <span class="text-[10px] text-gray-400 font-medium tracking-widest mt-1">DASHBOARD</span>
            </div>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
        <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">الرئيسية</p>
        
        <a href="{{ route('dashboard') }}" class="flex items-center gap-4 px-4 py-3.5 {{ request()->routeIs('dashboard') ? 'bg-accent text-white rounded-xl shadow-glow' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-xl' }} transition-all-300 group">
            <i class="fa-solid fa-grid-2 w-5 text-center text-lg"></i>
            <span class="font-medium">لوحة القيادة</span>
        </a>

        <a href="#" class="flex items-center gap-4 px-4 py-3.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all-300 group">
            <i class="fa-solid fa-images w-5 text-center text-lg group-hover:text-accent transition-colors"></i>
            <span class="font-medium">معرض الصور</span>
            <span class="mr-auto bg-primary-light text-xs py-0.5 px-2 rounded-md border border-white/10">جديد</span>
        </a>

        <a href="#" class="flex items-center gap-4 px-4 py-3.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all-300 group">
            <i class="fa-solid fa-qrcode w-5 text-center text-lg group-hover:text-accent transition-colors"></i>
            <span class="font-medium">ماسح QR</span>
        </a>

        <div class="my-6 border-t border-white/10"></div>
        
        <p class="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">النظام</p>

        <a href="{{ route('accounts.index') }}" class="flex items-center gap-4 px-4 py-3.5 {{ request()->routeIs('accounts.*') ? 'bg-accent text-white rounded-xl shadow-glow' : 'text-gray-400 hover:text-white hover:bg-white/5 rounded-xl' }} transition-all-300 group">
            <i class="fa-solid fa-users-gear w-5 text-center text-lg {{ request()->routeIs('accounts.*') ? '' : 'group-hover:text-accent' }} transition-colors"></i>
            <span class="font-medium">إدارة المستخدمين</span>
        </a>
        
        <a href="#" class="flex items-center gap-4 px-4 py-3.5 text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all-300 group">
            <i class="fa-solid fa-chart-line w-5 text-center text-lg group-hover:text-accent transition-colors"></i>
            <span class="font-medium">التقارير</span>
        </a>
    </nav>

    <div class="p-4 border-t border-white/10 bg-primary-dark/30">
        <button class="flex items-center gap-3 w-full p-2 hover:bg-white/5 rounded-lg transition-colors text-right">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(optional(auth()->user())->full_name ?? 'Admin User') }}&background=3b82f6&color=fff" class="w-10 h-10 rounded-full border-2 border-primary shadow-sm" alt="Admin">
            <div class="flex-1 overflow-hidden">
                <h4 class="text-sm font-bold text-white truncate">{{ optional(auth()->user())->full_name ?? 'إبراهيم الشامي' }}</h4>
                <p class="text-xs text-gray-400 truncate">{{ optional(auth()->user())->email ?? 'Senior Developer' }}</p>
            </div>
            <i class="fa-solid fa-chevron-left text-gray-500 text-xs"></i>
        </button>
    </div>
</aside>
