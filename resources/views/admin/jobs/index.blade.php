@extends('layouts.app')

@section('page-title', 'Research Jobs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage department research job postings.</p>
        @can('create', App\Models\Job::class)
            <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary">Add Job</a>
        @endcan
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.jobs.index'),
        'placeholder' => 'Search by title or department...',
    ])

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Last Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jobs as $job)
                        <tr>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->department?->name ?? '—' }}</td>
                            <td>{{ $job->last_date->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $job)
                                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $job)
                                    <form action="{{ route('admin.jobs.destroy', $job) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this job posting?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No research jobs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $jobs->links() }}
    </div>
@endsection
