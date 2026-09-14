@extends('layouts.app')

@section('page-title', 'Campus Publications')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage newsletters and campus news publications.</p>
        @can('create', App\Models\CampusPublication::class)
            <a href="{{ route('admin.campus-publications.create') }}" class="btn btn-primary">Add Publication</a>
        @endcan
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.campus-publications.index'),
        'placeholder' => 'Search by type, duration, or year...',
    ])

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.campus-publications.index') }}" class="row g-2 align-items-end">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <div class="col-md-4">
                    <label for="type" class="form-label small mb-1">Type</label>
                    <select name="type" id="type" class="form-select form-select-sm">
                        <option value="">All types</option>
                        @foreach (App\Models\CampusPublication::types() as $type)
                            <option value="{{ $type }}" @selected(request('type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                    <a href="{{ route('admin.campus-publications.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Duration</th>
                        <th>Year</th>
                        <th>File</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($publications as $publication)
                        <tr>
                            <td>{{ $publication->type }}</td>
                            <td>{{ $publication->duration }}</td>
                            <td>{{ $publication->year }}</td>
                            <td>
                                @if ($publication->fileUrl())
                                    <a href="{{ $publication->fileUrl() }}" target="_blank" rel="noopener noreferrer">
                                        {{ $publication->original_filename ?? 'View PDF' }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.campus-publications.show', $publication) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $publication)
                                    <a href="{{ route('admin.campus-publications.edit', $publication) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $publication)
                                    <form action="{{ route('admin.campus-publications.destroy', $publication) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this publication?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No publications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $publications->links() }}
    </div>
@endsection
