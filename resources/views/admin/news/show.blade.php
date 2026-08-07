@extends('layouts.app')

@section('page-title', 'News Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if ($news->image)
                <div class="mb-4">
                    <img src="{{ asset('storage/'.$news->image) }}" alt="{{ $news->title }}" class="img-fluid rounded" style="max-height: 300px;">
                </div>
            @endif

            <dl class="row mb-0">
                <dt class="col-sm-3">Title</dt>
                <dd class="col-sm-9">{{ $news->title }}</dd>

                <dt class="col-sm-3">Slug</dt>
                <dd class="col-sm-9"><code>{{ $news->slug }}</code></dd>

                <dt class="col-sm-3">Published Date</dt>
                <dd class="col-sm-9">{{ $news->published_date->format('d M Y') }}</dd>

                <dt class="col-sm-3">Department</dt>
                <dd class="col-sm-9">{{ $news->departmentLabel() }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if ($news->status)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Content</dt>
                <dd class="col-sm-9">
                    <div class="news-content border rounded p-3 bg-white">
                        {!! $news->sanitizedDescription() !!}
                    </div>
                </dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ $news->created_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        @can('update', $news)
            <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection

@push('styles')
    <style>
        .news-content h1,
        .news-content h2,
        .news-content h3 {
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .news-content p:last-child,
        .news-content ul:last-child,
        .news-content ol:last-child {
            margin-bottom: 0;
        }
    </style>
@endpush
