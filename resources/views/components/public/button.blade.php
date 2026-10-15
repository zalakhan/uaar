@props([
    'type' => 'submit',
    'variant' => 'primary',
])

<button type="{{ $type }}" class="public-btn public-btn--{{ $variant }}" {{ $attributes }}>
    {{ $slot }}
</button>
