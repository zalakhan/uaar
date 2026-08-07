@php
    $isEdit = isset($news);
    $statusValue = old('status', $isEdit ? $news->status : true);
    $departmentScope = old('department_scope', $selectedDepartmentScope ?? \App\Models\News::MAIN_WEBSITE_SCOPE);
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $news->title ?? '') }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="published_date" class="form-label">Published Date <span class="text-danger">*</span></label>
        <input type="date" name="published_date" id="published_date"
               class="form-control @error('published_date') is-invalid @enderror"
               value="{{ old('published_date', $isEdit ? $news->published_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('published_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="department_scope" class="form-label">Department <span class="text-danger">*</span></label>
        <select name="department_scope" id="department_scope" class="form-select @error('department_scope') is-invalid @enderror" required>
            <option value="{{ \App\Models\News::MAIN_WEBSITE_SCOPE }}" @selected((string) $departmentScope === \App\Models\News::MAIN_WEBSITE_SCOPE)>
                Main Website
            </option>
            @foreach ($departments as $id => $name)
                <option value="{{ $id }}" @selected((string) $departmentScope === (string) $id)>{{ $name }}</option>
            @endforeach
        </select>
        @error('department_scope')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Content <span class="text-danger">*</span></label>
        <textarea name="description" id="description" rows="8"
                  class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $news->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="image" class="form-label">Image</label>
        @if ($isEdit && $news->image)
            <div class="mb-2">
                <img src="{{ asset('storage/'.$news->image) }}" alt="Current image" class="img-thumbnail" style="max-height: 120px;">
            </div>
        @endif
        <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
        @if ($isEdit)
            <div class="form-text">Leave empty to keep the current image.</div>
        @endif
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label d-block">Status <span class="text-danger">*</span></label>
        <input type="hidden" name="status" value="0">
        <div class="form-check form-switch mt-2">
            <input type="checkbox" name="status" id="status" class="form-check-input @error('status') is-invalid @enderror"
                   value="1" @checked(filter_var($statusValue, FILTER_VALIDATE_BOOLEAN))>
            <label for="status" class="form-check-label">Active</label>
        </div>
        <div class="form-text">Inactive articles are hidden from public display.</div>
        @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>
</div>
