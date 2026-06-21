@extends('layouts.app')

@section('page-title', 'Departments')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage university departments.</p>
        @can('create', App\Models\Department::class)
            <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">Add Department</a>
        @endcan
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.departments.index'),
        'placeholder' => 'Search by department name...',
    ])

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Faculty</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departments as $department)
                        <tr>
                            <td>{{ $department->name }}</td>
                            <td>{{ $department->faculty?->name ?? '—' }}</td>
                            <td>
                                @if ($department->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.departments.show', $department) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $department)
                                    <a href="{{ route('admin.departments.edit', $department) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $department)
                                    <form action="{{ route('admin.departments.destroy', $department) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this department?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No departments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $departments->links() }}
    </div>
@endsection
