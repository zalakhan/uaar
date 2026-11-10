@props([
    'name',
    'id' => null,
    'label',
    'value' => null,
    'error' => null,
    'validationKey' => null,
    'rows' => 3,
])

@php
    $fieldId = $id ?? $name;
    $messageKey = $validationKey ?? $name;
    $isRequired = $attributes->has('required');
    $hasError = $errors->has($messageKey) || $error;
@endphp

<div class="public-field">
    <x-public.field-label :for="$fieldId" :required="$isRequired">{{ $label }}</x-public.field-label>
    <textarea
        name="{{ $name }}"
        id="{{ $fieldId }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'public-input'.($hasError ? ' is-invalid' : '')]) }}
    >{{ old($messageKey, $value ?? '') }}</textarea>
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
