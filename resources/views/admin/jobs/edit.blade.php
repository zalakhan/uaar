@extends('layouts.app')

@section('page-title', 'Edit Research Job')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.jobs.update', $job) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $job->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="last_date" class="form-label">Last Date</label>
                        <input type="date" name="last_date" id="last_date"
                               class="form-control @error('last_date') is-invalid @enderror"
                               value="{{ old('last_date', $job->last_date->format('Y-m-d')) }}" required>
                        @error('last_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="department_id" class="form-label">Department</label>
                        <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror">
                            <option value="" @selected(old('department_id', $job->department_id) === null || old('department_id', $job->department_id) === '')>Select</option>
                            @foreach ($departments as $id => $name)
                                <option value="{{ $id }}" @selected((string) old('department_id', $job->department_id) === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="job_file" class="form-label">Job File (PDF or Image)</label>
                        <div class="mb-2">
                            <a href="{{ asset('storage/'.$job->job_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View Current File</a>
                        </div>
                        <input type="file" name="job_file" id="job_file"
                               class="form-control @error('job_file') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png">
                        <div class="form-text">Leave empty to keep the current file.</div>
                        @error('job_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Job</button>
                    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
