@extends('layouts.app')

@section('page-title', 'Faculties')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage university faculties.</p>
        @can('create', App\Models\Faculty::class)
            <a href="{{ route('admin.faculties.create') }}" class="btn btn-primary">Add Faculty</a>
        @endcan
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.faculties.index'),
        'placeholder' => 'Search by faculty name...',
    ])

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Departments</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($faculties as $faculty)
                        <tr>
                            <td>{{ $faculty->name }}</td>
                            <td>{{ $faculty->departments_count }}</td>
                            <td>
                                @if ($faculty->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.faculties.show', $faculty) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $faculty)
                                    <a href="{{ route('admin.faculties.edit', $faculty) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $faculty)
                                    <form action="{{ route('admin.faculties.destroy', $faculty) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this faculty?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No faculties found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $faculties->links() }}
    </div>
@endsection
