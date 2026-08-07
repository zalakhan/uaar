@extends('layouts.app')

@section('page-title', 'Tender Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Category</dt>
                <dd class="col-sm-9">{{ $tender->categoryLabel() }}</dd>

                @if ($tender->title)
                    <dt class="col-sm-3">Title</dt>
                    <dd class="col-sm-9">{{ $tender->title }}</dd>
                @endif

                @if ($tender->tender_no)
                    <dt class="col-sm-3">Tender No.</dt>
                    <dd class="col-sm-9">{{ $tender->tender_no }}</dd>
                @endif

                <dt class="col-sm-3">Uploaded Date</dt>
                <dd class="col-sm-9">{{ $tender->uploaded_date->format('d M Y') }}</dd>

                <dt class="col-sm-3">Due Date</dt>
                <dd class="col-sm-9">{{ $tender->due_date?->format('d M Y') ?? '—' }}</dd>

                @if ($tender->description)
                    <dt class="col-sm-3">Description</dt>
                    <dd class="col-sm-9">{!! $tender->sanitizedDescription() !!}</dd>
                @endif

                <dt class="col-sm-3">File</dt>
                <dd class="col-sm-9">
                    <a href="{{ asset('storage/'.$tender->tender_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download / View File</a>
                </dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ $tender->created_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        @can('update', $tender)
            <a href="{{ route('admin.tenders.edit', $tender) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.tenders.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection
