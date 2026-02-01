<header class="bg-white/80 backdrop-blur-md border-b border-gray-100 px-4 sm:px-6 py-3 sticky top-0 z-40 transition-all duration-300 shadow-sm">
    @php
        $user = Auth::user();
        $userTypeCode = $user->user_type_code ?? 'admin';
        
        $profileRoute = route('profile');
        if ($userTypeCode === 'studio') $profileRoute = route('studio.profile.edit');
        if ($userTypeCode === 'school') $profileRoute = route('school.profile.edit');
        
        $passwordRoute = route('password.change');
        
        // Handle profile image
        $hasProfileImage = !empty($user->profile_image);
        $profileImageUrl = $hasProfileImage ? (str_starts_with($user->profile_image, 'http') ? $user->profile_image : asset('storage/' . $user->profile_image)) : null;
        $initials = substr($user->full_name ?? 'U', 0, 1);
    @endphp

    <div class="flex items-center justify-between gap-4 max-w-[1920px] mx-auto">
        <div class="flex items-center gap-2 sm:gap-6 flex-1">
            <!-- Mobile Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden w-10 h-10 flex items-center justify-center text-gray-500 hover:text-accent hover:bg-accent/5 rounded-xl transition-all active:scale-95">
                <i class="fas fa-bars text-lg"></i>
            </button>
            
            <div class="flex flex-col">
                <h2 class="text-base sm:text-xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-accent rounded-full hidden md:inline-block"></span>
                    <span class="truncate max-w-[150px] sm:max-w-none">@yield('title')</span>
                </h2>
                <p class="text-[9px] md:text-[10px] text-gray-400 font-bold uppercase tracking-wider mt-0.5 hidden xs:block">لوحة التحكم</p>
            </div>

            <!-- Enhanced Search Bar -->
            <div class="hidden lg:flex items-center max-w-md w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 focus-within:ring-2 focus-within:ring-accent/10 focus-within:border-accent/30 focus-within:bg-white transition-all group mr-4">
                <i class="fa-solid fa-search text-gray-300 text-sm ml-3 group-focus-within:text-accent transition-colors"></i>
                <input type="text" placeholder="بحث سريع..." class="bg-transparent border-none text-sm w-full focus:outline-none text-gray-600 placeholder-gray-400">
                <span class="text-[10px] font-bold text-gray-400 px-1.5 py-0.5 rounded border border-gray-200 bg-white shadow-sm group-focus-within:hidden">/</span>
            </div>
        </div>
        
        <div class="flex items-center gap-2 sm:gap-4">
            <!-- Notifications -->
            <button class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-accent hover:bg-accent/5 rounded-xl transition-all relative group" title="الإشعارات">
                <i class="fas fa-bell text-lg"></i>
                <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white ring-1 ring-red-100"></span>
            </button>
            
            <div class="h-6 w-px bg-gray-100 mx-1 hidden sm:block"></div>
            
            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button @click="open = !open" class="flex items-center gap-3 p-1 rounded-2xl hover:bg-gray-50 cursor-pointer transition-all focus:outline-none group">
                    <div class="text-right hidden sm:block px-1">
                        <p class="text-sm font-bold text-gray-900 truncate max-w-[120px]">{{ $user->full_name ?? 'المستخدم' }}</p>
                        <p class="text-[10px] text-gray-500 font-medium">{{ $user->type->name ?? 'مسؤول' }}</p>
                    </div>
                    
                    <div class="relative">
                        <div class="w-10 h-10 bg-gradient-to-br from-accent to-blue-600 rounded-xl flex items-center justify-center text-white shadow-md shadow-accent/10 overflow-hidden ring-2 ring-white group-hover:ring-accent/20 transition-all border border-gray-100">
                            @if($hasProfileImage)
                                <img src="{{ $profileImageUrl }}" class="w-full h-full object-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=3B82F6&color=fff'">
                            @else
                                <span class="text-sm font-black">{{ $initials }}</span>
                            @endif
                        </div>
                        <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <i class="fas fa-chevron-down text-gray-300 text-[10px] transition-transform duration-300 hidden sm:block" :class="open ? 'rotate-180 text-accent' : ''"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                     class="absolute top-full left-0 mt-2 w-56 bg-white rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.1)] border border-gray-100 overflow-hidden z-50 origin-top-left">
                    
                    <div class="p-4 bg-gray-50/50 border-b border-gray-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">الحساب</p>
                        <p class="text-xs font-bold text-gray-900 truncate leading-tight">{{ $user->full_name }}</p>
                        <p class="text-[10px] text-gray-500 truncate mt-0.5">{{ $user->email ?? '' }}</p>
                    </div>

                    <div class="p-2">
                        <a href="{{ $profileRoute }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-bold text-gray-600 hover:text-accent hover:bg-accent/5 rounded-xl transition-all">
                            <i class="fas fa-user-circle text-sm opacity-50"></i>
                            <span>الملف الشخصي</span>
                        </a>
                        <a href="{{ $passwordRoute }}" class="flex items-center gap-3 px-3 py-2.5 text-xs font-bold text-gray-600 hover:text-accent hover:bg-accent/5 rounded-xl transition-all">
                            <i class="fas fa-shield-alt text-sm opacity-50"></i>
                            <span>الأمان وكلمة السر</span>
                        </a>
                    </div>

                    <div class="p-2 border-t border-gray-50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 text-xs font-bold text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                <i class="fas fa-power-off text-sm opacity-70"></i>
                                <span>تسجيل الخروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
