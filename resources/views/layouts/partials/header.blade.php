<header class="h-20 bg-surface/80 backdrop-blur-md border-b border-gray-200 flex items-center justify-between px-6 lg:px-10 z-10 sticky top-0">
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-500 hover:text-primary rounded-lg hover:bg-gray-100">
            <i class="fa-solid fa-bars-staggered text-xl"></i>
        </button>
        <h1 class="text-xl lg:text-2xl font-bold text-primary hidden sm:block">@yield('page-title', 'نظرة عامة')</h1>
    </div>

    <div class="flex items-center gap-3 sm:gap-6">
        <div class="hidden md:flex items-center bg-gray-100 rounded-xl px-4 py-2.5 w-64 lg:w-96 focus-within:bg-white focus-within:ring-2 ring-accent/20 transition-all border border-transparent focus-within:border-accent/30">
            <i class="fa-solid fa-magnifying-glass text-gray-400 ml-3"></i>
            <input type="text" placeholder="بحث عن صورة، حدث، أو مستخدم..." class="bg-transparent w-full text-sm outline-none placeholder-gray-400 text-gray-700">
            <span class="text-xs text-gray-400 border border-gray-300 rounded px-1.5 py-0.5 hidden lg:block">Ctrl K</span>
        </div>

        <div class="flex items-center gap-2 sm:gap-3 border-r pr-3 sm:pr-6 border-gray-200 mr-2 sm:mr-4">
            <button class="relative p-2.5 text-gray-500 hover:bg-accent/10 hover:text-accent rounded-xl transition-colors">
                <i class="fa-regular fa-bell text-xl"></i>
                <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>
            <button class="p-2.5 text-gray-500 hover:bg-accent/10 hover:text-accent rounded-xl transition-colors hidden sm:block">
                <i class="fa-regular fa-envelope text-xl"></i>
            </button>
        </div>

        <button class="bg-primary hover:bg-primary-light text-white px-4 sm:px-6 py-2.5 rounded-xl font-semibold shadow-lg shadow-primary/20 flex items-center gap-2 transition-all active:scale-95">
            <i class="fa-solid fa-plus"></i>
            <span class="hidden sm:inline">رفع جديد</span>
        </button>
    </div>
</header>
