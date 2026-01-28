@props(['active' => false, 'icon'])

@php
    $baseClasses = 'relative flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-300 group overflow-hidden';
    $activeClasses = $active 
        ? 'bg-gradient-to-r from-accent to-blue-600 text-white shadow-lg shadow-blue-500/30 border border-white/10' 
        : 'text-gray-400 hover:text-white hover:bg-white/5 hover:border hover:border-white/5 border border-transparent';
    
    $iconActiveClasses = $active ? 'text-white' : 'text-blue-200/50 group-hover:text-accent transition-colors';
@endphp

<a {{ $attributes->merge(['class' => "$baseClasses $activeClasses"]) }}>
    @if($active)
        <div class="absolute inset-0 bg-white/20 blur-lg opacity-50"></div>
    @endif
    
    <div class="relative z-10 flex items-center gap-3 w-full">
        <div class="w-6 text-center">
            <i class="fas {{ $icon }} {{ $iconActiveClasses }} text-lg"></i>
        </div>
        <span class="font-medium flex-1">{{ $slot }}</span>
        
        @if($active)
           <div class="w-1.5 h-1.5 rounded-full bg-white shadow-lg animate-pulse"></div>
        @endif
    </div>
</a>
