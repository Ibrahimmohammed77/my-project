@props([
    'name',
    'label',
    'options' => [],
    'selected' => null,
    'placeholder' => 'اختر خياراً',
    'required' => false
])

<div>
    <label class="block text-sm font-semibold text-gray-700 mb-2">
        {{ $label }}
        @if($required) <span class="text-red-500">*</span> @endif
    </label>
    <select 
        name="{{ $name }}" 
        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all @error($name) border-red-500 @enderror"
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $value => $text)
            <option value="{{ $value }}" {{ (string)$value === (string)old($name, $selected) ? 'selected' : '' }}>
                {{ $text }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
