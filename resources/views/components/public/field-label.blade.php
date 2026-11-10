@props(['for', 'required' => false])

<label {{ $attributes->merge(['class' => 'public-label', 'for' => $for]) }}>
    {{ $slot }}
    @if ($required)
        <span class="public-required" aria-hidden="true">*</span>
    @endif
</label>
