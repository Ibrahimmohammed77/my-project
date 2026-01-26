<aside class="fixed inset-y-0 right-0 z-50 w-72 bg-gradient-to-b from-[#1D2B45] to-[#0F172A] text-white shadow-2xl transition-transform duration-300 lg:static lg:translate-x-0 flex flex-col"
    :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
    x-data="{
        contentOpen: false,
        identityOpen: true,
        reportsOpen: false,
        settingsOpen: false
    }">
    
    <!-- Logo Section -->
    <div class="h-28 flex items-center justify-center border-b border-white/5 bg-white/5 mx-4 mt-4 rounded-2xl mb-6">
        <div class="flex flex-col items-center">
            <div class="h-14 w-14 rounded-full overflow-hidden border-2 border-accent shadow-lg mb-2">
                <img src="{{ asset('images/logo-new.jpg') }}" alt="صوركم" class="h-full w-full object-cover">
            </div>
            <h1 class="text-lg font-bold tracking-tight">منصة صوركم</h1>
        </div>
        
        <!-- Mobile Close -->
        <button @click="sidebarOpen = false" class="lg:hidden absolute left-4 text-gray-400 hover:text-white">
            <i class="fa-solid fa-xmark text-xl"></i>
        </button>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto px-4 space-y-6 pb-6 scrollbar-none">
        
        <!-- Dashboard -->
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-2">الرئيسية</p>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-accent text-white shadow-lg shadow-accent/20' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                <i class="fa-solid fa-gauge w-5 text-center {{ request()->routeIs('dashboard') ? '' : 'text-gray-400 group-hover:text-accent' }}"></i>
                <span class="font-medium">لوحة التحكم</span>
            </a>
        </div>

        <!-- Content Management -->
        <div>
            <button @click="contentOpen = !contentOpen" class="w-full flex items-center justify-between px-3 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                <div class="flex items-center gap-3">
                    <i class="fa-regular fa-folder w-5 text-center text-gray-400 group-hover:text-accent"></i>
                    <span class="font-medium">إدارة المحتوى</span>
                </div>
                <i class="fa-solid fa-chevron-down text-xs text-gray-500 transition-transform duration-200" :class="contentOpen ? 'rotate-180' : ''"></i>
            </button>
            
            <div x-show="contentOpen" x-collapse class="space-y-1 mt-1 mr-3 border-r border-white/10 pr-3">
                <a href="#" class="flex items-center justify-between px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">
                    <span>الفعاليات</span>
                    <span class="text-[10px] bg-white/10 px-1.5 py-0.5 rounded text-gray-300">قريباً</span>
                </a>
                <a href="#" class="flex items-center justify-between px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">
                    <span>الصور</span>
                    <span class="text-[10px] bg-white/10 px-1.5 py-0.5 rounded text-gray-300">قريباً</span>
                </a>
            </div>
        </div>

        <!-- Identity Management -->
        <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 px-2 mt-6">المستخدمين</p>
            <button @click="identityOpen = !identityOpen" class="w-full flex items-center justify-between px-3 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-users w-5 text-center text-gray-400 group-hover:text-accent"></i>
                    <span class="font-medium">إدارة المستخدمين</span>
                </div>
                <i class="fa-solid fa-chevron-down text-xs text-gray-500 transition-transform duration-200" :class="identityOpen ? 'rotate-180' : ''"></i>
            </button>
            
            <div x-show="identityOpen" x-collapse class="space-y-1 mt-1 mr-3 border-r border-white/10 pr-3">
                <a href="{{ route('spa.accounts') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('spa.accounts') ? 'text-accent bg-accent/10 font-bold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span>الحسابات</span>
                </a>
                <a href="{{ route('spa.roles') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('spa.roles') ? 'text-accent bg-accent/10 font-bold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span>الأدوار</span>
                </a>
                <a href="{{ route('spa.permissions') }}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-all {{ request()->routeIs('spa.permissions') ? 'text-accent bg-accent/10 font-bold' : 'text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <span>الصلاحيات</span>
                </a>
            </div>
        </div>

        <!-- Reports -->
        <div>
            <button @click="reportsOpen = !reportsOpen" class="w-full flex items-center justify-between px-3 py-2 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all group">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-gray-400 group-hover:text-accent"></i>
                    <span class="font-medium">التقارير</span>
                </div>
                <i class="fa-solid fa-chevron-down text-xs text-gray-500 transition-transform duration-200" :class="reportsOpen ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="reportsOpen" x-collapse class="space-y-1 mt-1 mr-3 border-r border-white/10 pr-3">
                <a href="#" class="flex items-center justify-between px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-white/5 transition-all">
                    <span>الإحصائيات</span>
                </a>
            </div>
        </div>
    </div>

    <!-- User Profile -->
    <div class="p-4 bg-black/20 mt-auto border-t border-white/5">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-accent/20 flex items-center justify-center text-accent border border-accent/30 font-bold">
                 {{ substr(Auth::user()->full_name ?? 'U', 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white truncate">{{ Auth::user()->full_name ?? 'مستخدم' }}</p>
                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-400 transition-colors p-2" title="تسجيل الخروج">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
