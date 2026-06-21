@extends('layouts.app')

@section('page-title', 'Department Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $department->name }}</dd>

                <dt class="col-sm-3">Slug</dt>
                <dd class="col-sm-9"><code>{{ $department->slug }}</code></dd>

                <dt class="col-sm-3">Faculty</dt>
                <dd class="col-sm-9">{{ $department->faculty?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if ($department->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ $department->created_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        @can('update', $department)
            <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection
