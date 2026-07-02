@extends('layouts.app')

@section('page-title', 'Staff Members')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage departmental staff profiles.</p>
        <div class="d-flex gap-2">
            @can('viewAny', App\Models\StaffMember::class)
                @if (auth()->user()->can('staff_members.edit'))
                    <a href="{{ route('admin.staff-members.order.index') }}" class="btn btn-outline-primary">Manage Staff Order</a>
                @endif
            @endcan
            @can('create', App\Models\StaffMember::class)
                <a href="{{ route('admin.staff-members.create') }}" class="btn btn-primary">Add Staff Member</a>
            @endcan
        </div>
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.staff-members.index'),
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
                                <a href="{{ route('admin.staff-members.show', $member) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $member)
                                    <a href="{{ route('admin.staff-members.edit', $member) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $member)
                                    <form action="{{ route('admin.staff-members.destroy', $member) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this staff member?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No staff members found.</td>
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
