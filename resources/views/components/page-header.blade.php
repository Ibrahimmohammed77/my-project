@props(['title', 'count' => null])

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white/80 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-white/60 mb-6 relative overflow-hidden">
    <div class="flex items-center gap-4 relative z-10">
        <div class="h-12 w-1.5 bg-gradient-to-b from-accent to-blue-600 rounded-full shadow-lg shadow-blue-500/30"></div>
        <div>
            <h2 class="text-xl font-black text-gray-800 flex items-center gap-3 tracking-tight">
                {{ $title }}
                @if($count !== null)
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-xs font-bold text-accent border border-blue-100 shadow-sm" id="header-count">{{ $count }}</span>
                @endif
            </h2>
        </div>
    </div>
    
    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto relative z-10">
        {{ $slot }}
    </div>

    <!-- Decorative bg -->
    <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-gradient-to-br from-blue-50 to-transparent rounded-full blur-3xl opacity-50 pointer-events-none"></div>
</div>
