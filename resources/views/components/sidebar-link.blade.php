@props(['active', 'icon'])

@php
    $classes = 'nav-link flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group ' . ($active ? 'active' : '');
    $iconClasses = 'text-blue-200 group-hover:text-white transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <div class="icon-wrapper">
        <i class="fas {{ $icon }} {{ $iconClasses }}"></i>
    </div>
    <div class="flex-1 flex items-center justify-between gap-2">
        <span class="font-medium text-white group-hover:text-blue-50 transition-colors">{{ $slot }}</span>
    </div>
</a>
