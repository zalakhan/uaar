@php
    $isRequired = $requireFacultyFields ?? false;
    $hideSortOrder = $hideSortOrder ?? false;
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Name @if($isRequired)<span class="text-danger">*</span>@endif</label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $member->name ?? '') }}" required maxlength="255">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="designation" class="form-label">Designation @if($isRequired)<span class="text-danger">*</span>@endif</label>
        <input type="text" name="designation" id="designation" class="form-control @error('designation') is-invalid @enderror"
               value="{{ old('designation', $member->designation ?? '') }}" @if($isRequired) required @endif maxlength="255">
        @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="faculty_id" class="form-label">Faculty @if($isRequired)<span class="text-danger">*</span>@endif</label>
        <select name="faculty_id" id="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror" @if($isRequired) required @endif>
            <option value="">@if($isRequired) Select faculty @else No faculty @endif</option>
            @foreach ($faculties as $id => $name)
                <option value="{{ $id }}" @selected(old('faculty_id', $selectedFacultyId ?? $member->faculty_id ?? '') == $id)>{{ $name }}</option>
            @endforeach
        </select>
        @error('faculty_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="department_id" class="form-label">Department @if($isRequired)<span class="text-danger">*</span>@endif</label>
        <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
            <option value="">Select department</option>
        </select>
        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">Email @if($isRequired)<span class="text-danger">*</span>@endif</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $member->email ?? '') }}" @if($isRequired) required @endif maxlength="255">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
               value="{{ old('phone', $member->phone ?? '') }}" maxlength="50">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="mobile" class="form-label">Mobile</label>
        <input type="text" name="mobile" id="mobile" class="form-control @error('mobile') is-invalid @enderror"
               value="{{ old('mobile', $member->mobile ?? '') }}" maxlength="50">
        @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="qualification" class="form-label">Qualification @if($isRequired)<span class="text-danger">*</span>@endif</label>
        <input type="text" name="qualification" id="qualification" class="form-control @error('qualification') is-invalid @enderror"
               value="{{ old('qualification', $member->qualification ?? '') }}" @if($isRequired) required @endif maxlength="255">
        @error('qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="total_experience" class="form-label">Experience (years)</label>
        <input type="number" name="total_experience" id="total_experience" min="0" max="99"
               class="form-control @error('total_experience') is-invalid @enderror"
               value="{{ old('total_experience', $member->total_experience ?? '') }}">
        @error('total_experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="total_publication" class="form-label">Publications</label>
        <input type="number" name="total_publication" id="total_publication" min="0" max="9999"
               class="form-control @error('total_publication') is-invalid @enderror"
               value="{{ old('total_publication', $member->total_publication ?? '') }}">
        @error('total_publication')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="additional_department" class="form-label">Additional Department</label>
        <input type="text" name="additional_department" id="additional_department"
               class="form-control @error('additional_department') is-invalid @enderror"
               value="{{ old('additional_department', $member->additional_department ?? '') }}" maxlength="255">
        @error('additional_department')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="additional_designation" class="form-label">Additional Designation</label>
        <input type="text" name="additional_designation" id="additional_designation"
               class="form-control @error('additional_designation') is-invalid @enderror"
               value="{{ old('additional_designation', $member->additional_designation ?? '') }}" maxlength="255">
        @error('additional_designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12">
        <label for="address" class="form-label">Address</label>
        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" maxlength="5000">{{ old('address', $member->address ?? '') }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12">
        <label for="bio" class="form-label">Bio</label>
        <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror" maxlength="5000">{{ old('bio', $member->bio ?? '') }}</textarea>
        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @unless ($hideSortOrder)
        <div class="col-md-3">
            <label for="sort_order" class="form-label">Sort Order</label>
            <input type="number" name="sort_order" id="sort_order" min="0" max="9999"
                   class="form-control @error('sort_order') is-invalid @enderror"
                   value="{{ old('sort_order', $member->sort_order ?? 0) }}">
            @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endunless

    <div class="{{ $hideSortOrder ? 'col-md-12' : 'col-md-9' }}">
        <label for="photo" class="form-label">Photo</label>
        <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
        @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if (!empty($member?->photo))
            <small class="text-muted">Current photo is saved. Upload a new file to replace it.</small>
        @endif
    </div>

    <div class="col-md-12">
        <div class="d-flex flex-wrap gap-4">
            <div class="form-check">
                <input type="checkbox" name="is_hec" id="is_hec" class="form-check-input" value="1"
                       @checked(old('is_hec', $member->is_hec ?? false))>
                <label for="is_hec" class="form-check-label">HEC Approved</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_studyleave" id="is_studyleave" class="form-check-input" value="1"
                       @checked(old('is_studyleave', $member->is_studyleave ?? false))>
                <label for="is_studyleave" class="form-check-label">On Study Leave</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_onleave" id="is_onleave" class="form-check-input" value="1"
                       @checked(old('is_onleave', $member->is_onleave ?? false))>
                <label for="is_onleave" class="form-check-label">On Leave</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                       @checked(old('is_active', $member->is_active ?? true))>
                <label for="is_active" class="form-check-label">Active</label>
            </div>
        </div>
    </div>
</div>

@include('admin.members._faculty-department-script')
