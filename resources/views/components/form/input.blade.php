@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false
])

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    <input 
        type="{{ $type }}" 
        name="{{ $name }}" 
        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all @error($name) border-red-500 @enderror" 
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
