@php
    $isEdit = isset($tender);
    $selectedCategory = old('category', $tender->category ?? \App\Models\Tender::CATEGORY_SELECT);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
        <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
            <option value="{{ \App\Models\Tender::CATEGORY_SELECT }}" @selected($selectedCategory === \App\Models\Tender::CATEGORY_SELECT)>Select</option>
            @foreach (\App\Models\Tender::categories() as $value => $label)
                <option value="{{ $value }}" @selected($selectedCategory === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 d-none" id="field-title">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $tender->title ?? '') }}" disabled>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 d-none" id="field-tender-no">
        <label for="tender_no" class="form-label">Tender No. <span class="text-danger">*</span></label>
        <input type="text" name="tender_no" id="tender_no" class="form-control @error('tender_no') is-invalid @enderror"
               value="{{ old('tender_no', $tender->tender_no ?? '') }}" disabled>
        @error('tender_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 d-none" id="field-description">
        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
        <textarea name="description" id="description" rows="6"
                  class="form-control @error('description') is-invalid @enderror" disabled>{{ old('description', $tender->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 d-none" id="field-uploaded-date">
        <label for="uploaded_date" class="form-label">Uploaded Date <span class="text-danger">*</span></label>
        <input type="date" name="uploaded_date" id="uploaded_date"
               class="form-control @error('uploaded_date') is-invalid @enderror"
               value="{{ old('uploaded_date', $isEdit ? $tender->uploaded_date->format('Y-m-d') : date('Y-m-d')) }}" disabled>
        @error('uploaded_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 d-none" id="field-due-date">
        <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
        <input type="date" name="due_date" id="due_date"
               class="form-control @error('due_date') is-invalid @enderror"
               value="{{ old('due_date', $isEdit ? $tender->due_date?->format('Y-m-d') : '') }}" disabled>
        @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 d-none" id="field-tender-file">
        <label for="tender_file" class="form-label">Tender File (PDF or Image) <span class="text-danger">*</span></label>
        @if ($isEdit && $tender->tender_file)
            <div class="mb-2">
                <a href="{{ asset('storage/'.$tender->tender_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View Current File</a>
            </div>
        @endif
        <input type="file" name="tender_file" id="tender_file"
               class="form-control @error('tender_file') is-invalid @enderror"
               accept=".pdf,.jpg,.jpeg,.png"
               data-required-on-create="{{ $isEdit ? '0' : '1' }}"
               @unless($isEdit) disabled @endunless>
        @if ($isEdit)
            <div class="form-text">Leave empty to keep the current file.</div>
        @endif
        @error('tender_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
