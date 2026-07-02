@extends('layouts.app')

@section('page-title', 'Designation Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $designation->name }}</dd>

                <dt class="col-sm-3">Slug</dt>
                <dd class="col-sm-9"><code>{{ $designation->slug }}</code></dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ $designation->created_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        @can('update', $designation)
            <a href="{{ route('admin.designations.edit', $designation) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.designations.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection
