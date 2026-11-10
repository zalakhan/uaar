@props([
    'name',
    'id' => null,
    'label',
    'error' => null,
    'validationKey' => null,
])

@php
    $fieldId = $id ?? $name;
    $messageKey = $validationKey ?? $name;
    $isRequired = $attributes->has('required');
    $hasError = $errors->has($messageKey) || $error;
@endphp

<div class="public-field">
    <x-public.field-label :for="$fieldId" :required="$isRequired">{{ $label }}</x-public.field-label>
    <input
        type="file"
        name="{{ $name }}"
        id="{{ $fieldId }}"
        {{ $attributes->merge(['class' => 'public-input public-input--file'.($hasError ? ' is-invalid' : '')]) }}
    >
    @error($messageKey)
        <p class="public-field-error" role="alert">{{ $message }}</p>
    @else
        @if ($error)
            <p class="public-field-error" role="alert">{{ $error }}</p>
        @else
            <p class="public-field-error public-field-error--client" data-client-error role="alert">This field is required.</p>
        @endif
    @enderror
</div>
