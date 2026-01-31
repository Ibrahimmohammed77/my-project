<aside class="fixed inset-y-0 right-0 z-50 w-72 bg-[#0F172A] text-white shadow-2xl transition-transform duration-300 lg:static lg:translate-x-0 flex flex-col relative overflow-hidden group/sidebar"
    :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
    x-data="{
        contentOpen: false,
        identityOpen: true,
        reportsOpen: false,
        settingsOpen: false
    }">
    
    <!-- Background Effects -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-blue-600/20 rounded-full blur-[100px] pointer-events-none mix-blend-screen"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-indigo-600/20 rounded-full blur-[100px] pointer-events-none mix-blend-screen"></div>
    <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent pointer-events-none"></div>

    <!-- Content Container -->
    <div class="relative z-10 flex flex-col h-full bg-slate-900/40 backdrop-blur-xl border-l border-white/5">
        
        <!-- Hero Logo Section -->
        <div class="relative flex flex-col items-center justify-center pt-8 pb-6 px-6">
            <div class="relative mb-3 group">
                <div class="absolute inset-0 bg-accent/20 rounded-full blur-xl group-hover:blur-2xl transition-all duration-500"></div>
                <div class="relative h-20 w-20 rounded-full overflow-hidden ring-4 ring-white/10 shadow-2xl transform group-hover:scale-105 transition-all duration-500">
                    <img src="{{ asset('images/logo-new.jpg') }}" alt="صوركم" class="h-full w-full object-cover">
                </div>
            </div>
            <h1 class="text-xl font-black tracking-tight text-white drop-shadow-lg">
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-200 to-white">منصة صوركم</span>
            </h1>
        </div>

        <!-- Navigation -->
        <div class="flex-1 overflow-y-auto px-4 space-y-2 py-4 scrollbar-none">
            
            <!-- Dashboard -->
            <div class="mb-6">
                <p class="text-[10px] font-bold text-blue-300/60 uppercase tracking-widest mb-3 px-3">الرئيسية</p>
                <x-sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="fa-gauge">
                    لوحة التحكم
                </x-sidebar-link>
            </div>

            <!-- Content Management -->
            <div class="mb-6">
                <p class="text-[10px] font-bold text-blue-300/60 uppercase tracking-widest mb-3 px-3">المحتوى</p>
                
                <div class="space-y-1">
                    <button @click="contentOpen = !contentOpen" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all group duration-300">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-accent/20 transition-colors">
                                <i class="fa-regular fa-folder w-4 text-center text-gray-400 group-hover:text-accent transition-colors"></i>
                            </div>
                            <span class="font-medium text-sm">إدارة المحتوى</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-300" :class="contentOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="contentOpen" x-collapse class="pl-4 pr-3 space-y-1">
                        <div class="relative border-r border-white/10 pr-4 py-1 space-y-1">
                            <a href="#" class="flex items-center justify-between px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-white/5 transition-all group/item">
                                <span class="group-hover/item:translate-x-1 transition-transform">الفعاليات</span>
                                <span class="text-[9px] bg-white/10 px-1.5 py-0.5 rounded text-gray-400 border border-white/5">قريباً</span>
                            </a>
                            <a href="#" class="flex items-center justify-between px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-white/5 transition-all group/item">
                                <span class="group-hover/item:translate-x-1 transition-transform">الصور</span>
                                <span class="text-[9px] bg-white/10 px-1.5 py-0.5 rounded text-gray-400 border border-white/5">قريباً</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entities Management (Admin Only) -->
            @if(Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin'))
            <div class="mb-6">
                <p class="text-[10px] font-bold text-blue-300/60 uppercase tracking-widest mb-3 px-3">الجهات</p>
                
                <div class="space-y-1">
                    <x-sidebar-link href="{{ route('spa.studios') }}" :active="request()->routeIs('spa.studios')" icon="fa-building">
                        الاستوديوهات
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('spa.schools') }}" :active="request()->routeIs('spa.schools')" icon="fa-school">
                        المدارس
                    </x-sidebar-link>
                    <x-sidebar-link href="{{ route('spa.accounts') }}" :active="request()->routeIs('spa.accounts')" icon="fa-users">
                        المشاركين
                    </x-sidebar-link>
                </div>
            </div>

            <!-- System Settings (Admin Only) -->
            <div class="mb-6">
                <p class="text-[10px] font-bold text-blue-300/60 uppercase tracking-widest mb-3 px-3">النظام</p>
                
                <div class="space-y-1">
                    <button @click="identityOpen = !identityOpen" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all group duration-300">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-accent/20 transition-colors">
                                <i class="fa-solid fa-cog w-4 text-center text-gray-400 group-hover:text-accent transition-colors"></i>
                            </div>
                            <span class="font-medium text-sm">الإعدادات</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-300" :class="identityOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="identityOpen" x-collapse class="pl-4 pr-3 space-y-1">
                        <div class="relative border-r border-white/10 pr-4 py-1 space-y-1">
                            <x-sidebar-link href="{{ route('spa.roles') }}" :active="request()->routeIs('spa.roles')" icon="fa-shield-halved" class="text-sm">
                                الأدوار
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('spa.permissions') }}" :active="request()->routeIs('spa.permissions')" icon="fa-key" class="text-sm">
                                الصلاحيات
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('spa.lookups') }}" :active="request()->routeIs('spa.lookups')" icon="fa-list-ul" class="text-sm">
                                القوائم
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('spa.plans') }}" :active="request()->routeIs('spa.plans')" icon="fa-box-archive" class="text-sm">
                                الخطط
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('spa.subscriptions') }}" :active="request()->routeIs('spa.subscriptions')" icon="fa-calendar-check" class="text-sm">
                                الاشتراكات
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('spa.cards') }}" :active="request()->routeIs('spa.cards')" icon="fa-id-card-clip" class="text-sm">
                                إدارة الكروت
                            </x-sidebar-link>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Studio Management (Studio Owner Only) -->
            @if(Auth::user()->hasRole('studio_owner'))
            <div class="mb-6">
                <p class="text-[10px] font-bold text-blue-300/60 uppercase tracking-widest mb-3 px-3">إدارة الاستوديو</p>
                
                <div class="space-y-5">
                    <!-- Operations -->
                    <div>
                        <p class="text-[9px] font-bold text-blue-400/40 uppercase tracking-wider mb-2 px-3">العمليات</p>
                        <div class="space-y-1">
                            <x-sidebar-link href="{{ route('studio.albums.index') }}" :active="request()->routeIs('studio.albums.*')" icon="fa-images">
                                الألبومات
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('studio.customers.index') }}" :active="request()->routeIs('studio.customers.*')" icon="fa-users">
                                العملاء
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('studio.photo-review.pending') }}" :active="request()->routeIs('studio.photo-review.*')" icon="fa-clipboard-check">
                                مراجعة الصور
                            </x-sidebar-link>
                        </div>
                    </div>

                    <!-- Resources -->
                    <div>
                        <p class="text-[9px] font-bold text-blue-400/40 uppercase tracking-wider mb-2 px-3">الموارد</p>
                        <div class="space-y-1">
                            <x-sidebar-link href="{{ route('studio.cards.index') }}" :active="request()->routeIs('studio.cards.*')" icon="fa-id-card">
                                الكروت
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('studio.storage.index') }}" :active="request()->routeIs('studio.storage.*')" icon="fa-database">
                                تخصيص المساحة
                            </x-sidebar-link>
                        </div>
                    </div>
                    <!-- Settings -->
                    <div>
                        <p class="text-[9px] font-bold text-blue-400/40 uppercase tracking-wider mb-2 px-3">الإعدادات</p>
                        <div class="space-y-1">
                            <x-sidebar-link href="{{ route('studio.profile.edit') }}" :active="request()->routeIs('studio.profile.*')" icon="fa-user-pen">
                                بيانات الاستوديو
                            </x-sidebar-link>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- School Management (School Owner Only) -->
            @if(Auth::user()->hasRole('school_owner'))
            <div class="mb-6">
                <p class="text-[10px] font-bold text-blue-300/60 uppercase tracking-widest mb-3 px-3">إدارة المدرسة</p>
                
                <div class="space-y-5">
                    <!-- Operations -->
                    <div>
                        <p class="text-[9px] font-bold text-blue-400/40 uppercase tracking-wider mb-2 px-3">العمليات</p>
                        <div class="space-y-1">
                            <x-sidebar-link href="{{ route('school.albums.index') }}" :active="request()->routeIs('school.albums.*')" icon="fa-images">
                                الألبومات
                            </x-sidebar-link>
                            <x-sidebar-link href="{{ route('school.students.index') }}" :active="request()->routeIs('school.students.*')" icon="fa-user-graduate">
                                الطلاب
                            </x-sidebar-link>
                        </div>
                    </div>

                    <!-- Resources -->
                    <div>
                        <p class="text-[9px] font-bold text-blue-400/40 uppercase tracking-wider mb-2 px-3">الموارد</p>
                        <div class="space-y-1">
                            <x-sidebar-link href="{{ route('school.cards.index') }}" :active="request()->routeIs('school.cards.*')" icon="fa-id-card">
                                الكروت
                            </x-sidebar-link>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div>
                        <p class="text-[9px] font-bold text-blue-400/40 uppercase tracking-wider mb-2 px-3">الإعدادات</p>
                        <div class="space-y-1">
                            <x-sidebar-link href="{{ route('school.profile.edit') }}" :active="request()->routeIs('school.profile.*')" icon="fa-school-flag">
                                بيانات المدرسة
                            </x-sidebar-link>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Reports -->
            <div>
                <button @click="reportsOpen = !reportsOpen" class="w-full flex items-center justify-between px-4 py-3 text-gray-300 hover:text-white hover:bg-white/5 rounded-xl transition-all group duration-300">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center group-hover:bg-accent/20 transition-colors">
                            <i class="fa-solid fa-chart-pie w-4 text-center text-gray-400 group-hover:text-accent transition-colors"></i>
                        </div>
                        <span class="font-medium text-sm">التقارير</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-500 transition-transform duration-300" :class="reportsOpen ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="reportsOpen" x-collapse class="pl-4 pr-3 space-y-1">
                    <div class="relative border-r border-white/10 pr-4 py-1 space-y-1">
                        <a href="#" class="flex items-center justify-between px-3 py-2 text-sm text-gray-400 hover:text-white rounded-lg hover:bg-white/5 transition-all group/item">
                            <span class="group-hover/item:translate-x-1 transition-transform">الإحصائيات</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="p-4 bg-black/20 mt-auto border-t border-white/5 backdrop-blur-md">
            <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-white/5 transition-colors cursor-pointer group">
                <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-accent to-blue-600 flex items-center justify-center text-white font-bold shadow-lg ring-2 ring-white/10">
                     {{ substr(optional(Auth::user())->full_name ?? 'U', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate group-hover:text-blue-200 transition-colors">{{ optional(Auth::user())->full_name ?? 'مستخدم' }}</p>
                    <p class="text-[10px] text-gray-400 truncate">{{ optional(Auth::user())->email ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-red-400 hover:bg-red-400/10 p-2 rounded-lg transition-all" title="تسجيل الخروج">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
