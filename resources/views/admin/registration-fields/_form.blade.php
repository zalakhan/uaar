<div class="mb-3">
    <label class="form-label">Label <span class="text-danger">*</span></label>
    <input type="text" name="label" class="form-control" value="{{ old('label', $field->label ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Field key</label>
    <input type="text" name="field_key" class="form-control" value="{{ old('field_key', $field->field_key ?? '') }}" placeholder="Auto-generated from label if empty">
</div>
<div class="mb-3">
    <label class="form-label">Type</label>
    <select name="field_type" class="form-select" id="field_type">
        @foreach ($fieldTypes as $value => $label)
            <option value="{{ $value }}" @selected(old('field_type', $field->field_type ?? 'text') === $value)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3" id="options-block">
    <label class="form-label">Select options (one per line)</label>
    <textarea name="options_text" class="form-control" rows="3">@if(isset($field) && $field->options){{ implode("\n", $field->options) }}@endif</textarea>
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $field->is_active ?? true))>
    <label class="form-check-label" for="is_active">Active</label>
</div>

@push('scripts')
<script>
    const typeSelect = document.getElementById('field_type');
    const optionsBlock = document.getElementById('options-block');
    function toggleOptions() {
        optionsBlock.classList.toggle('d-none', typeSelect.value !== 'select');
    }
    typeSelect.addEventListener('change', toggleOptions);
    toggleOptions();
</script>
@endpush
