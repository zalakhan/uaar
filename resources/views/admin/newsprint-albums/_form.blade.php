@php $isEdit = isset($album); @endphp

<div class="row g-3">
    <div class="col-md-8">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $album->title ?? '') }}" required maxlength="255">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="publish_date" class="form-label">Publish Date <span class="text-danger">*</span></label>
        <input type="date" name="publish_date" id="publish_date" class="form-control @error('publish_date') is-invalid @enderror"
               value="{{ old('publish_date', $isEdit ? $album->publish_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('publish_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="1" @selected(old('status', $album->status ?? true))>Active</option>
            <option value="0" @selected(!old('status', $album->status ?? true))>Inactive</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
