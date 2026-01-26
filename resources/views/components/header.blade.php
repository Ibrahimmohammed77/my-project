<header class="bg-white/80 backdrop-blur-md border-b border-gray-100 px-6 py-4 sticky top-0 z-30 transition-all duration-300">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4 flex-1">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-10 h-10 flex items-center justify-center text-gray-500 hover:text-primary hover:bg-gray-100 rounded-xl transition-all">
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <div class="hidden md:block">
                <h2 class="text-xl font-bold text-gray-800 tracking-tight">@yield('title')</h2>
                <!-- Breadcrumbs could go here -->
            </div>

            <!-- Search Bar -->
            <div class="hidden lg:flex items-center max-w-md w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus-within:ring-2 focus-within:ring-accent/20 focus-within:border-accent transition-all mr-6">
                <i class="fa-solid fa-search text-gray-400 text-sm ml-3"></i>
                <input type="text" placeholder="بحث في النظام..." class="bg-transparent border-none text-sm w-full focus:outline-none text-gray-700 placeholder-gray-400">
                <span class="text-xs text-gray-400 border border-gray-200 rounded px-1.5 py-0.5 hidden xl:block">ctrl + k</span>
            </div>
        </div>
        
        <div class="flex items-center gap-3 sm:gap-4">
            <!-- Quick Actions -->
            <div class="flex items-center gap-2">
                <button class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-primary hover:bg-gray-100 rounded-xl transition-all relative group" title="الإشعارات">
                    <i class="fas fa-bell text-lg group-hover:animate-swing"></i>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                </button>
                
                <button class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-primary hover:bg-gray-100 rounded-xl transition-all md:hidden">
                    <i class="fas fa-search text-lg"></i>
                </button>

                <button class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-primary hover:bg-gray-100 rounded-xl transition-all hidden sm:flex" title="الإعدادات">
                    <i class="fas fa-cog text-lg hover:rotate-90 transition-transform duration-500"></i>
                </button>
            </div>
            
            <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
            
            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 pl-2 py-1 pr-1 rounded-xl hover:bg-gray-50 cursor-pointer transition-all border border-transparent hover:border-gray-100 group focus:outline-none">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-bold text-gray-800 leading-none mb-1">{{ optional(Auth::user())->full_name ?? 'المستخدم' }}</p>
                        <p class="text-[10px] text-gray-500 font-medium bg-gray-100 px-1.5 py-0.5 rounded inline-block">مسؤول النظام</p>
                    </div>
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-br from-accent to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-accent/20 overflow-hidden border-2 border-white ring-1 ring-gray-100 group-hover:ring-accent/30 transition-all">
                            @if(optional(Auth::user())->profile_image)
                                <img src="{{ Auth::user()->profile_image }}" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-user text-sm"></i>
                            @endif
                        </div>
                        <!-- Status Indicator -->
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-2"
                     class="absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50"
                     style="display: none;">
                    
                    <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                        <p class="text-sm font-bold text-gray-800">حسابي</p>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ optional(Auth::user())->email ?? '' }}</p>
                    </div>

                    <div class="p-2">
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-user-circle w-5 text-center"></i>
                            الملف الشخصي
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-600 hover:text-primary hover:bg-gray-50 rounded-lg transition-colors">
                            <i class="fas fa-cog w-5 text-center"></i>
                            الإعدادات
                        </a>
                    </div>

                    <div class="border-t border-gray-100 p-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors font-medium">
                                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
