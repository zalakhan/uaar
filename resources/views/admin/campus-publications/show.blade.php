@extends('layouts.app')

@section('page-title', 'Campus Publication Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Type</dt>
                <dd class="col-sm-9">{{ $publication->type }}</dd>

                <dt class="col-sm-3">Duration</dt>
                <dd class="col-sm-9">{{ $publication->duration }}</dd>

                <dt class="col-sm-3">Year</dt>
                <dd class="col-sm-9">{{ $publication->year }}</dd>

                <dt class="col-sm-3">File</dt>
                <dd class="col-sm-9">
                    @if ($publication->fileUrl())
                        <a href="{{ $publication->fileUrl() }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                            {{ $publication->original_filename ?? 'Download / View PDF' }}
                        </a>
                    @else
                        —
                    @endif
                </dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ $publication->created_at->format('d M Y, h:i A') }}</dd>

                <dt class="col-sm-3">Updated</dt>
                <dd class="col-sm-9">{{ $publication->updated_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        @can('update', $publication)
            <a href="{{ route('admin.campus-publications.edit', $publication) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.campus-publications.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection
