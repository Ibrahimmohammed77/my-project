@props(['label', 'name', 'type' => 'text', 'required' => false, 'icon' => null])

<div class="w-full">
    <label class="block text-xs font-bold text-gray-700 mb-1.5">{{ $label }} @if($required)<span class="text-red-500">*</span>@endif</label>
    <div class="relative">
        @if($icon)
            <i class="fas {{ $icon }} absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
        @endif
        
        <input 
            type="{{ $type }}" 
            id="{{ $name }}" 
            name="{{ $name }}"
            {{ $attributes->merge(['class' => 'w-full ' . ($icon ? 'pr-8 pl-4' : 'px-4') . ' py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm']) }}
            @if($required) required @endif
        >
    </div>
    <p id="{{ $name }}-error" class="text-red-500 text-xs mt-1 hidden field-error"></p>
</div>
