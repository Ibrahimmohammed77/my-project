@props([
    'name',
    'label',
    'options' => [],
    'selected' => null,
    'placeholder' => 'Select an option',
    'required' => false
])

<div class="mb-3">
    <label class="form-label">
        {{ $label }}
        @if($required) <span class="text-danger">*</span> @endif
    </label>
    <select 
        name="{{ $name }}" 
        class="form-select @error($name) is-invalid @enderror"
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
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
