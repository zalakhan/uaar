@extends('layouts.app')

@section('page-title', 'Faculty Members')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage teaching faculty profiles.</p>
        <div class="d-flex gap-2">
            @can('viewAny', App\Models\FacultyMember::class)
                @if (auth()->user()->can('faculty_members.edit'))
                    <a href="{{ route('admin.faculty-members.order.index') }}" class="btn btn-outline-primary">Manage Faculty Order</a>
                @endif
            @endcan
            @can('create', App\Models\FacultyMember::class)
                <a href="{{ route('admin.faculty-members.create') }}" class="btn btn-primary">Add Faculty Member</a>
            @endcan
        </div>
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.faculty-members.index'),
        'placeholder' => 'Search by name, designation, email, or department...',
    ])

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($members as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->designation?->name ?? '—' }}</td>
                            <td>{{ $member->department?->name ?? '—' }}</td>
                            <td>
                                @if ($member->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @can('view', $member)
                                    <a href="{{ route('admin.faculty-members.publications.index', $member) }}" class="btn btn-sm btn-outline-info">Manage Publications</a>
                                    <a href="{{ route('admin.faculty-members.awards.index', $member) }}" class="btn btn-sm btn-outline-info">Manage Awards</a>
                                @endcan
                                <a href="{{ route('admin.faculty-members.show', $member) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $member)
                                    <a href="{{ route('admin.faculty-members.edit', $member) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $member)
                                    <form action="{{ route('admin.faculty-members.destroy', $member) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this faculty member?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No faculty members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $members->links() }}
    </div>
@endsection
