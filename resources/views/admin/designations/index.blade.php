@extends('layouts.app')

@section('page-title', 'Designations')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage member designations.</p>
        @can('create', App\Models\Designation::class)
            <a href="{{ route('admin.designations.create') }}" class="btn btn-primary">Add Designation</a>
        @endcan
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.designations.index'),
        'placeholder' => 'Search by designation name...',
    ])

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($designations as $designation)
                        <tr>
                            <td>{{ $designation->name }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.designations.show', $designation) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $designation)
                                    <a href="{{ route('admin.designations.edit', $designation) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $designation)
                                    <form action="{{ route('admin.designations.destroy', $designation) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this designation?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">No designations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $designations->links() }}
    </div>
@endsection
