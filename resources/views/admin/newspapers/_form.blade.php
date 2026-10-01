<div class="mb-3">
    <label for="newspaper_name" class="form-label">Newspaper Name <span class="text-danger">*</span></label>
    <input type="text" name="newspaper_name" id="newspaper_name" class="form-control @error('newspaper_name') is-invalid @enderror"
           value="{{ old('newspaper_name', $newspaper->newspaper_name ?? '') }}" required maxlength="255">
    @error('newspaper_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
