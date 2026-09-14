@php
    $isEdit = isset($publication);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
            <option value="">Select</option>
            @foreach (App\Models\CampusPublication::types() as $type)
                <option value="{{ $type }}" @selected(old('type', $publication->type ?? '') === $type)>{{ $type }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
        <input type="number" name="year" id="year" min="2014" max="{{ now()->year + 1 }}"
               class="form-control @error('year') is-invalid @enderror"
               value="{{ old('year', $publication->year ?? now()->year) }}" required>
        @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="duration" class="form-label">Duration <span class="text-danger">*</span></label>
        <input type="text" name="duration" id="duration" maxlength="255"
               class="form-control @error('duration') is-invalid @enderror"
               value="{{ old('duration', $publication->duration ?? '') }}" required>
        @error('duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="file" class="form-label">PDF File <span class="text-danger">*</span></label>
        @if ($isEdit && $publication->fileUrl())
            <div class="mb-2">
                <a href="{{ $publication->fileUrl() }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary">
                    View Current File
                </a>
                @if ($publication->original_filename)
                    <span class="text-muted small ms-2">{{ $publication->original_filename }}</span>
                @endif
            </div>
        @endif
        <input type="file" name="file" id="file"
               class="form-control @error('file') is-invalid @enderror"
               accept="application/pdf,.pdf"
               @unless($isEdit) required @endunless>
        @if ($isEdit)
            <div class="form-text">Leave empty to keep the current file.</div>
        @endif
        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
