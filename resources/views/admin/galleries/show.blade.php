@extends('layouts.app')

@section('page-title', 'Gallery Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $gallery->name }}</dd>

                <dt class="col-sm-3">Slug</dt>
                <dd class="col-sm-9"><code>{{ $gallery->slug }}</code></dd>

                <dt class="col-sm-3">Date</dt>
                <dd class="col-sm-9">{{ $gallery->date->format('d M Y') }}</dd>

                <dt class="col-sm-3">Department</dt>
                <dd class="col-sm-9">{{ $gallery->department?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if ($gallery->status)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Photos</dt>
                <dd class="col-sm-9">{{ $gallery->photos->count() }} photo(s)</dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ $gallery->created_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>

    @if ($gallery->photos->isNotEmpty())
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white">
                <h2 class="h6 mb-0">Preview</h2>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach ($gallery->photos->take(6) as $photo)
                        <div class="col-md-2">
                            <img src="{{ asset('storage/'.$photo->photo) }}" alt="Gallery photo" class="img-thumbnail w-100">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('admin.galleries.photos.index', $gallery) }}" class="btn btn-info text-white">Manage Photos</a>
        @can('update', $gallery)
            <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection
