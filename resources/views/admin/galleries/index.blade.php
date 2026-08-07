@extends('layouts.app')

@section('page-title', 'Photo Gallery')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage photo gallery albums.</p>
        @can('create', App\Models\Gallery::class)
            <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">Add Gallery</a>
        @endcan
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.galleries.index'),
        'placeholder' => 'Search by gallery name or department...',
    ])

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($galleries as $gallery)
                        <tr>
                            <td>{{ $gallery->name }}</td>
                            <td>{{ $gallery->date->format('d M Y') }}</td>
                            <td>{{ $gallery->department?->name ?? '—' }}</td>
                            <td>
                                @if ($gallery->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.galleries.show', $gallery) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                <a href="{{ route('admin.galleries.photos.index', $gallery) }}" class="btn btn-sm btn-outline-info">Photos</a>
                                @can('update', $gallery)
                                    <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $gallery)
                                    <form action="{{ route('admin.galleries.destroy', $gallery) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this gallery and all its photos?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No galleries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $galleries->links() }}
    </div>
@endsection
