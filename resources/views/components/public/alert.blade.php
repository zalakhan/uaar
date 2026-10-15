@props([
    'type' => 'success',
])

<div {{ $attributes->merge(['class' => 'public-alert public-alert--'.$type, 'role' => 'alert']) }}>
    {{ $slot }}
</div>
