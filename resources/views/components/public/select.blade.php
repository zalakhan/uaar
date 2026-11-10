@props([
    'name',
    'id' => null,
    'label',
    'options' => [],
    'placeholder' => null,
    'value' => null,
    'error' => null,
    'validationKey' => null,
])

@php
    $fieldId = $id ?? $name;
    $messageKey = $validationKey ?? $name;
    $selected = old($messageKey, $value);
    $isRequired = $attributes->has('required');
    $hasError = $errors->has($messageKey) || $error;
@endphp

<div class="public-field">
    <x-public.field-label :for="$fieldId" :required="$isRequired">{{ $label }}</x-public.field-label>
    <select
        name="{{ $name }}"
        id="{{ $fieldId }}"
        class="public-select @if($hasError) is-invalid @endif"
        {{ $attributes }}
    >
        @if ($placeholder)
            <option value="" disabled @selected($selected === null || $selected === '') hidden>{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $selected === (string) $optionValue)>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>
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
