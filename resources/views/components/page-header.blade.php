@props([
    'title', 
    'subtitle' => null,
    'count' => null, 
    'backUrl' => null,
    'breadcrumbs' => [] // Array of ['label' => '...', 'url' => '...']
])

<div class="relative mb-8 group">
    <!-- Main Header Card -->
    <div class="bg-white/95 backdrop-blur-xl p-6 sm:p-8 rounded-[2rem] shadow-card border border-white/60 relative overflow-hidden transition-all duration-500 hover:shadow-soft">
        
        <!-- Decoration -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-accent/5 rounded-full blur-3xl group-hover:bg-accent/10 transition-colors duration-700"></div>
        <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-blue-500/5 rounded-full blur-2xl group-hover:bg-blue-500/10 transition-colors duration-700"></div>

        <div class="relative z-10">
            <!-- Breadcrumbs -->
            @if(!empty($breadcrumbs))
                <nav class="flex items-center gap-2 mb-4 text-xs font-bold tracking-wide uppercase transition-all duration-300">
                    <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-accent flex items-center gap-1.5">
                        <i class="fa-solid fa-house-chimney text-[10px]"></i>
                        <span>الرئيسية</span>
                    </a>
                    @foreach($breadcrumbs as $breadcrumb)
                        <i class="fa-solid fa-chevron-left text-[8px] text-gray-300 mx-1"></i>
                        @if($loop->last)
                            <span class="text-gray-500">{{ $breadcrumb['label'] }}</span>
                        @else
                            <a href="{{ $breadcrumb['url'] }}" class="text-gray-400 hover:text-accent transition-colors">
                                {{ $breadcrumb['label'] }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            @endif

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <!-- Title & Subtitle Area -->
                <div class="flex items-start gap-5">
                    @if($backUrl)
                        <a href="{{ $backUrl }}" class="mt-1 w-11 h-11 flex items-center justify-center rounded-2xl bg-gray-50 border border-gray-100 text-gray-400 hover:text-accent hover:border-accent/30 hover:bg-accent/5 transition-all duration-300 group/back shadow-sm shrink-0">
                            <i class="fa-solid fa-arrow-right text-lg group-hover/back:translate-x-1 transition-transform"></i>
                        </a>
                    @else
                        <div class="mt-1 w-3 h-12 bg-gradient-to-b from-accent to-blue-600 rounded-full shadow-lg shadow-blue-500/20 shrink-0"></div>
                    @endif

                    <div class="space-y-1.5">
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                                {{ $title }}
                            </h1>
                            @if($count !== null)
                                <span class="px-3 py-1 rounded-xl bg-accent/10 text-accent text-sm font-black border border-accent/10 shadow-sm" id="header-count">
                                    {{ $count }}
                                </span>
                            @endif
                        </div>
                        @if($subtitle)
                            <p class="text-gray-500 text-sm sm:text-base font-medium leading-relaxed max-w-2xl italic">
                                {{ $subtitle }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Actions Slot -->
                <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full lg:w-auto lg:justify-end">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
