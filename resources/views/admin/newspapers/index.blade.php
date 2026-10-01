@extends('layouts.app')

@section('page-title', 'Newspapers')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage newspaper names for print clippings.</p>
        @can('create', App\Models\Newspaper::class)
            <a href="{{ route('admin.newspapers.create') }}" class="btn btn-primary">Add Newspaper</a>
        @endcan
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.newspapers.index'),
        'placeholder' => 'Search by newspaper name...',
    ])

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Newspaper Name</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($newspapers as $newspaper)
                        <tr>
                            <td>{{ $newspaper->newspaper_name }}</td>
                            <td class="text-end">
                                @can('update', $newspaper)
                                    <a href="{{ route('admin.newspapers.edit', $newspaper) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $newspaper)
                                    <form action="{{ route('admin.newspapers.destroy', $newspaper) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this newspaper?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="text-center text-muted py-4">No newspapers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $newspapers->links() }}</div>
@endsection
