@extends('layouts.app')

@section('page-title', 'Add Department')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.departments.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="faculty_id" class="form-label">Faculty</label>
                        <select name="faculty_id" id="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror">
                            <!-- <option value="">No faculty</option> -->
                            @foreach ($faculties as $id => $name)
                                <option value="{{ $id }}" @selected(old('faculty_id') == $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('faculty_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1"
                                   @checked(old('is_active', true))>
                            <label for="is_active" class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Department</button>
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
