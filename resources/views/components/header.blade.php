<header class="bg-white/70 backdrop-blur-xl border-b border-white/40 px-6 py-4 sticky top-0 z-40 transition-all duration-300 shadow-[0_4px_30px_rgba(0,0,0,0.03)]">
    @php
        $user = Auth::user();
        $userTypeCode = $user->user_type_code ?? 'admin';
        
        // Determine profile route based on user type
        $profileRoute = route('profile');
        if ($userTypeCode === 'studio') $profileRoute = route('studio.profile.edit');
        if ($userTypeCode === 'school') $profileRoute = route('school.profile.edit');
        
        $passwordRoute = route('password.change');
    @endphp

    <div class="flex items-center justify-between gap-4 max-w-[1920px] mx-auto">
        <div class="flex items-center gap-6 flex-1">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-11 h-11 flex items-center justify-center text-gray-500 hover:text-accent hover:bg-accent/10 rounded-2xl transition-all shadow-sm active:scale-95">
                <i class="fas fa-bars text-lg"></i>
            </button>
            
            <div class="flex flex-col">
                <h2 class="text-lg md:text-xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-accent rounded-full hidden md:inline-block"></span>
                    @yield('title')
                </h2>
                <p class="text-[9px] md:text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5">لوحة التحكم</p>
            </div>

            <!-- Enhanced Search Bar -->
            <div class="hidden lg:flex items-center max-w-lg w-full bg-gray-100/50 backdrop-blur-sm border border-gray-200/50 rounded-2xl px-5 py-3 focus-within:ring-4 focus-within:ring-accent/10 focus-within:border-accent/30 focus-within:bg-white transition-all group mr-4">
                <i class="fa-solid fa-search text-gray-400 text-sm ml-3 group-focus-within:text-accent group-focus-within:scale-110 transition-all"></i>
                <input type="text" placeholder="بحث ذكي في النظام..." class="bg-transparent border-none text-sm w-full focus:outline-none text-gray-700 placeholder-gray-400 font-medium">
                <div class="flex items-center gap-1">
                    <span class="text-[10px] font-bold text-gray-400 bg-white border border-gray-200 rounded-md px-1.5 py-0.5 shadow-sm group-focus-within:border-accent/20 transition-all">CTRL</span>
                    <span class="text-[10px] font-bold text-gray-400 bg-white border border-gray-200 rounded-md px-1.5 py-0.5 shadow-sm group-focus-within:border-accent/20 transition-all">K</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-4 sm:gap-6">
            <!-- Quick Actions -->
            <div class="flex items-center gap-2.5">
                <button class="w-11 h-11 flex items-center justify-center text-gray-500 hover:text-accent hover:bg-accent/10 rounded-2xl transition-all relative group" title="الإشعارات">
                    <i class="fas fa-bell text-lg group-hover:animate-swing"></i>
                    <span class="absolute top-3 right-3 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white group-hover:scale-125 transition-all"></span>
                    <span class="absolute top-3 right-3 w-2.5 h-2.5 bg-red-500 rounded-full animate-ping opacity-20"></span>
                </button>
                
                <button class="w-11 h-11 flex items-center justify-center text-gray-500 hover:text-accent hover:bg-accent/10 rounded-2xl transition-all md:hidden active:scale-95 shadow-sm">
                    <i class="fas fa-search text-lg"></i>
                </button>

                <button class="w-11 h-11 flex items-center justify-center text-gray-500 hover:text-accent hover:bg-accent/10 rounded-2xl transition-all hidden sm:flex group" title="الإعدادات">
                    <i class="fas fa-cog text-lg group-hover:rotate-90 transition-transform duration-700"></i>
                </button>
            </div>
            
            <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
            
            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="flex items-center gap-4 pl-3 py-1.5 pr-1.5 rounded-2xl hover:bg-white hover:shadow-lg hover:shadow-gray-200/50 cursor-pointer transition-all border border-transparent hover:border-gray-100 group focus:outline-none active:scale-95">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-extrabold text-gray-900 leading-none mb-1.5 group-hover:text-accent transition-colors">{{ $user->full_name ?? 'المستخدم' }}</p>
                        <div class="flex items-center justify-end gap-1.5">
                            <span class="inline-block w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            <p class="text-[10px] text-gray-500 font-bold bg-gray-100 px-2 py-0.5 rounded-full uppercase tracking-tighter">
                                {{ $user->type->name ?? 'مسؤول النظام' }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <div class="w-11 h-11 bg-gradient-to-br from-accent to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-accent/20 overflow-hidden border-2 border-white ring-2 ring-gray-100 group-hover:ring-accent/30 group-hover:scale-105 transition-all duration-300">
                            @if($user->profile_image)
                                <img src="{{ $user->profile_image_url }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-sm font-bold">{{ substr($user->full_name ?? 'U', 0, 1) }}</span>
                            @endif
                        </div>
                        <!-- Status Indicator with Ring -->
                        <div class="absolute -bottom-0.5 -right-0.5 w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-sm ring-2 ring-transparent group-hover:ring-green-100 transition-all"></div>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-[10px] transition-transform duration-500" :class="open ? 'rotate-180 text-accent' : ''"></i>
                </button>

                <!-- Premium Dropdown Menu -->
                <div x-show="open" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                     class="absolute top-full left-0 mt-3 w-64 bg-white/95 backdrop-blur-xl rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden z-50 transform origin-top-left"
                     style="display: none;">
                    
                    <div class="p-6 border-b border-gray-50 bg-gradient-to-br from-gray-50 to-white">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">الحساب الشخصي</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-accent/10 rounded-xl flex items-center justify-center text-accent">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $user->full_name }}</p>
                                <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ $user->email ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3">
                        <a href="{{ $profileRoute }}" class="flex items-center justify-between px-4 py-3 text-sm text-gray-600 hover:text-accent hover:bg-accent/5 rounded-2xl transition-all group/item">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 group-hover/item:bg-accent/10 transition-colors">
                                    <i class="fas fa-user-edit text-xs"></i>
                                </div>
                                <span class="font-bold">تعديل الملف</span>
                            </div>
                            <i class="fas fa-chevron-left text-[10px] opacity-0 group-hover/item:opacity-100 transition-all -translate-x-2 group-hover/item:translate-x-0"></i>
                        </a>
                        
                        <a href="{{ $passwordRoute }}" class="flex items-center justify-between px-4 py-3 text-sm text-gray-600 hover:text-accent hover:bg-accent/5 rounded-2xl transition-all group/item">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 flex items-center justify-center rounded-xl bg-gray-50 group-hover/item:bg-accent/10 transition-colors">
                                    <i class="fas fa-key text-xs"></i>
                                </div>
                                <span class="font-bold">تغيير كلمة السر</span>
                            </div>
                            <i class="fas fa-chevron-left text-[10px] opacity-0 group-hover/item:opacity-100 transition-all -translate-x-2 group-hover/item:translate-x-0"></i>
                        </a>
                    </div>

                    <div class="p-3 bg-gray-50/50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-4 px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-2xl transition-all font-bold group/btn">
                                <div class="w-8 h-8 flex items-center justify-center rounded-xl bg-red-100/50 group-hover/btn:bg-red-100 transition-colors">
                                    <i class="fas fa-power-off text-xs"></i>
                                </div>
                                <span>تسجيل الخروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
