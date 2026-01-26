@props(['title', 'count' => null])

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-200 mb-6">
    <div class="flex items-center gap-3">
        <div class="h-10 w-1 bg-accent rounded-full"></div>
        <div>
            <h2 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                {{ $title }}
                @if($count !== null)
                    <span class="px-2 py-0.5 rounded-lg bg-gray-100 text-xs text-gray-600 border border-gray-200" id="header-count">{{ $count }}</span>
                @endif
            </h2>
        </div>
    </div>
    
    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
        {{ $slot }}
    </div>
</div>
