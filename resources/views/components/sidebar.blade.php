<aside 
    class="fixed inset-y-0 right-0 z-50 w-72 sidebar-gradient text-white transform transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-auto shadow-2xl lg:shadow-none flex flex-col" 
    :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
>
    <!-- Logo -->
    <div class="h-24 flex items-center justify-center border-b border-white/10 bg-black/10">
        <div class="flex items-center gap-4 w-full px-6">
            <div class="relative w-10 h-10 flex-shrink-0 group cursor-pointer overflow-hidden rounded-xl bg-white/5 border border-white/10 hover:border-accent/50 transition-all duration-300">
                <img src="{{ asset('images/logo.jpg') }}" alt="صورك" class="w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-110 transition-all duration-500">
            </div>
            <div class="flex flex-col justify-center">
                <h1 class="text-lg font-bold text-white tracking-wide">صورك</h1>
                <p class="text-[10px] text-blue-200/60 font-medium">لوحة التحكم</p>
            </div>
        </div>
        <!-- Close Button (Mobile) -->
        <button @click="sidebarOpen = false" class="lg:hidden absolute left-4 text-gray-400 hover:text-white transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1 scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-transparent">
        <div class="mb-4">
            <p class="text-xs font-semibold text-blue-200/60 px-4 mb-2">القائمة الرئيسية</p>
        </div>
        
        <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="fa-home">
            الرئيسية
        </x-sidebar-link>

        <div class="mt-6 mb-4">
            <p class="text-xs font-semibold text-blue-200/60 px-4 mb-2">إدارة الهوية</p>
        </div>

        <x-sidebar-link href="{{ route('spa.accounts') }}" :active="request()->routeIs('spa.accounts')" icon="fa-users" subtext="إدارة المستخدمين">
            الحسابات
        </x-sidebar-link>

        <x-sidebar-link href="{{ route('spa.roles') }}" :active="request()->routeIs('spa.roles')" icon="fa-user-shield" subtext="تحديد الصلاحيات">
            الأدوار
        </x-sidebar-link>

        <x-sidebar-link href="{{ route('spa.permissions') }}" :active="request()->routeIs('spa.permissions')" icon="fa-key" subtext="التحكم بالوصول">
            الصلاحيات
        </x-sidebar-link>
    </div>
    
    <!-- User Profile (Bottom) -->
    <div class="p-4 border-t border-white/10 bg-black/10">
        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-white/5 transition-all duration-300 cursor-pointer group">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-accent to-blue-400 p-[2px]">
                    <div class="w-full h-full rounded-full bg-primary flex items-center justify-center font-bold text-white">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-primary rounded-full"></div>
            </div>
            
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-white group-hover:text-accent transition-colors truncate">{{ Auth::user()->name ?? 'مستخدم' }}</p>
                <p class="text-[10px] text-blue-200/60 font-medium truncate">{{ Auth::user()->email ?? '' }}</p>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-400 hover:bg-white/10 transition-all opacity-0 group-hover:opacity-100 transform translate-x-2 group-hover:translate-x-0" title="تسجيل الخروج">
                    <i class="fas fa-sign-out-alt text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</aside>
