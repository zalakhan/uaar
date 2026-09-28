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
        <label for="designation_id" class="form-label">Designation @if($isRequired)<span class="text-danger">*</span>@endif</label>
        <select name="designation_id" id="designation_id" class="form-select @error('designation_id') is-invalid @enderror" @if($isRequired) required @endif>
            <option value="">Select designation</option>
            @foreach ($designations as $id => $name)
                <option value="{{ $id }}" @selected(old('designation_id', $member->designation_id ?? '') == $id)>{{ $name }}</option>
            @endforeach
        </select>
        @error('designation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="faculty_id" class="form-label">Faculty @if($isRequired)<span class="text-danger">*</span>@endif</label>
        <select name="faculty_id" id="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror" @if($isRequired) required @endif>
            <!-- <option value="">@if($isRequired) Select faculty @else No faculty @endif</option> -->
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
        <label for="email" class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $member->email ?? '') }}" maxlength="255">
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

    <div class="{{ ($requireFacultyFields ?? false) ? 'col-12' : 'col-md-6' }}">
        <label for="qualification" class="form-label">Qualification @if($isRequired)<span class="text-danger">*</span>@endif</label>
        @if ($requireFacultyFields ?? false)
            <textarea name="qualification" id="qualification" rows="6"
                      class="form-control @error('qualification') is-invalid @enderror" @if($isRequired) required @endif>{{ old('qualification', $member->qualification ?? '') }}</textarea>
        @else
            <input type="text" name="qualification" id="qualification" class="form-control @error('qualification') is-invalid @enderror"
                   value="{{ old('qualification', $member->qualification ?? '') }}" @if($isRequired) required @endif maxlength="255">
        @endif
        @error('qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if ($requireFacultyFields ?? false)
    <div class="col-12">
        <label for="specialization" class="form-label">Specialization</label>
        <textarea name="specialization" id="specialization" rows="3" class="form-control @error('specialization') is-invalid @enderror" maxlength="5000">{{ old('specialization', $member->specialization ?? '') }}</textarea>
        @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @endif

    <div class="col-md-3">
        <label for="total_experience" class="form-label">Experience (years)</label>
        <input type="number" name="total_experience" id="total_experience" min="0" max="99"
               class="form-control @error('total_experience') is-invalid @enderror"
               value="{{ old('total_experience', $member->total_experience ?? '') }}">
        @error('total_experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="total_publication" class="form-label">@if($requireFacultyFields ?? false)Total Publications @else Publications @endif</label>
        <input type="number" name="total_publication" id="total_publication" min="0" max="9999"
               class="form-control @error('total_publication') is-invalid @enderror"
               value="{{ old('total_publication', $member->total_publication ?? '') }}">
        @error('total_publication')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="additional_department_id" class="form-label">Additional Department</label>
        <select name="additional_department_id" id="additional_department_id" class="form-select @error('additional_department_id') is-invalid @enderror">
            <option value="">No additional department</option>
            @foreach ($departments as $id => $name)
                <option value="{{ $id }}" @selected(old('additional_department_id', $member->additional_department_id ?? '') == $id)>{{ $name }}</option>
            @endforeach
        </select>
        @error('additional_department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="additional_designation_id" class="form-label">Additional Designation</label>
        <select name="additional_designation_id" id="additional_designation_id" class="form-select @error('additional_designation_id') is-invalid @enderror">
            <option value="">No additional designation</option>
            @foreach ($designations as $id => $name)
                <option value="{{ $id }}" @selected(old('additional_designation_id', $member->additional_designation_id ?? '') == $id)>{{ $name }}</option>
            @endforeach
        </select>
        @error('additional_designation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12">
        <label for="address" class="form-label">Address</label>
        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" maxlength="5000">{{ old('address', $member->address ?? '') }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12">
        <label for="bio" class="form-label">@if($requireFacultyFields ?? false)Research Interest @else Bio @endif</label>
        <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror" maxlength="5000">{{ old('bio', $member->bio ?? '') }}</textarea>
        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    @if ($requireFacultyFields ?? false)

    <div class="col-md-12">
        <label for="research_link" class="form-label">WoS / ORCID / Google Scholar / Scopus / ResearchGate / Personal Web</label>
        <textarea name="research_link" id="research_link" rows="2" class="form-control @error('research_link') is-invalid @enderror" maxlength="5000">{{ old('research_link', $member->research_link ?? '') }}</textarea>
        @error('research_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12">
        <label for="research_group" class="form-label">Research Group</label>
        <textarea name="research_group" id="research_group" rows="2" class="form-control @error('research_group') is-invalid @enderror" maxlength="5000">{{ old('research_group', $member->research_group ?? '') }}</textarea>
        @error('research_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12">
        <label for="affiliation" class="form-label">Affiliation</label>
        <textarea name="affiliation" id="affiliation" rows="2" class="form-control @error('affiliation') is-invalid @enderror" maxlength="5000">{{ old('affiliation', $member->affiliation ?? '') }}</textarea>
        @error('affiliation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="projects_ongoing" class="form-label">No of Research Projects (Ongoing)</label>
        <input type="number" name="projects_ongoing" id="projects_ongoing" min="0"
               class="form-control @error('projects_ongoing') is-invalid @enderror"
               value="{{ old('projects_ongoing', $member->projects_ongoing ?? '') }}">
        @error('projects_ongoing')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="projects_completed" class="form-label">No of Research Projects (Completed)</label>
        <input type="number" name="projects_completed" id="projects_completed" min="0"
               class="form-control @error('projects_completed') is-invalid @enderror"
               value="{{ old('projects_completed', $member->projects_completed ?? '') }}">
        @error('projects_completed')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="supervision_phd" class="form-label">Research Supervision (PhD)</label>
        <input type="number" name="supervision_phd" id="supervision_phd" min="0"
               class="form-control @error('supervision_phd') is-invalid @enderror"
               value="{{ old('supervision_phd', $member->supervision_phd ?? '') }}">
        @error('supervision_phd')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="supervision_mphil_ms_msc" class="form-label">Research Supervision (MPhil / MS / M.Sc.)</label>
        <input type="number" name="supervision_mphil_ms_msc" id="supervision_mphil_ms_msc" min="0"
               class="form-control @error('supervision_mphil_ms_msc') is-invalid @enderror"
               value="{{ old('supervision_mphil_ms_msc', $member->supervision_mphil_ms_msc ?? '') }}">
        @error('supervision_mphil_ms_msc')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12">
        <label for="patent" class="form-label">Technologies Developed/ Patent/IP etc.</label>
        <textarea name="patent" id="patent" rows="2" class="form-control @error('patent') is-invalid @enderror" maxlength="5000">{{ old('patent', $member->patent ?? '') }}</textarea>
        @error('patent')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12">
        <label for="consultancy_services" class="form-label">Area(s) of Consultancy Services</label>
        <textarea name="consultancy_services" id="consultancy_services" rows="2" class="form-control @error('consultancy_services') is-invalid @enderror" maxlength="5000">{{ old('consultancy_services', $member->consultancy_services ?? '') }}</textarea>
        @error('consultancy_services')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    @endif

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
