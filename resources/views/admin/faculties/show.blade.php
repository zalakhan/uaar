@extends('layouts.app')

@section('page-title', 'Faculty Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $faculty->name }}</dd>

                <dt class="col-sm-3">Slug</dt>
                <dd class="col-sm-9"><code>{{ $faculty->slug }}</code></dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if ($faculty->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Departments</dt>
                <dd class="col-sm-9">
                    @if ($faculty->departments->isEmpty())
                        —
                    @else
                        <ul class="mb-0">
                            @foreach ($faculty->departments as $department)
                                <li>{{ $department->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ $faculty->created_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        @can('update', $faculty)
            <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection
