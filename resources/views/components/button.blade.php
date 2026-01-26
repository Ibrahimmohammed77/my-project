@props(['type' => 'submit', 'variant' => 'primary'])

@php
    $baseClass = "flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed";
    
    $variants = [
        'primary' => 'bg-accent text-white hover:bg-accent-hover shadow-accent/20',
        'secondary' => 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 shadow-red-500/20',
        'ghost' => 'bg-transparent text-gray-500 hover:bg-gray-50 hover:text-gray-900 shadow-none'
    ];
    
    $class = $baseClass . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</button>
