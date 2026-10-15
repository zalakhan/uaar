@props([
    'name',
    'id' => null,
    'label',
    'options' => [],
    'placeholder' => null,
    'value' => null,
    'error' => null,
])

@php
    $fieldId = $id ?? $name;
    $selected = old($name, $value);
@endphp

<div class="public-field">
    <label class="public-label" for="{{ $fieldId }}">{{ $label }}</label>
    <select
        name="{{ $name }}"
        id="{{ $fieldId }}"
        class="public-select @error($name) is-invalid @enderror {{ $error ? 'is-invalid' : '' }}"
        {{ $attributes }}
    >
        @if ($placeholder)
            <option value="" disabled @selected($selected === null || $selected === '')>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $selected === (string) $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="public-field-error">{{ $message }}</p>
    @else
        @if ($error)
            <p class="public-field-error">{{ $error }}</p>
        @endif
    @enderror
</div>
