@extends('layouts.app')

@section('page-title', 'Research Job Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Title</dt>
                <dd class="col-sm-9">{{ $job->title }}</dd>

                <dt class="col-sm-3">Department</dt>
                <dd class="col-sm-9">{{ $job->department?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Last Date</dt>
                <dd class="col-sm-9">{{ $job->last_date->format('d M Y') }}</dd>

                <dt class="col-sm-3">File</dt>
                <dd class="col-sm-9">
                    <a href="{{ asset('storage/'.$job->job_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download / View File</a>
                </dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ $job->created_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        @can('update', $job)
            <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection
