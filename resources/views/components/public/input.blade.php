@props([
    'name',
    'id' => null,
    'label',
    'type' => 'text',
    'value' => null,
    'error' => null,
])

@php
    $fieldId = $id ?? $name;
@endphp

<div class="public-field">
    <label class="public-label" for="{{ $fieldId }}">{{ $label }}</label>
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $fieldId }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'public-input'.( $errors->has($name) || $error ? ' is-invalid' : '')]) }}
    >
    @error($name)
        <p class="public-field-error">{{ $message }}</p>
    @else
        @if ($error)
            <p class="public-field-error">{{ $error }}</p>
        @endif
    @enderror
</div>
