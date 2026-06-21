@extends('layouts.app')

@section('page-title', 'Users')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage department admins and staff accounts.</p>
        @can('create', App\Models\User::class)
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $user->roles->pluck('name')->first() ?? '—' }}</span>
                            </td>
                            <td>{{ $user->department?->name ?? '—' }}</td>
                            <td class="text-end">
                                @if (auth()->user()->isSuperAdmin())
                                    <a href="{{ route('admin.users.permissions.edit', $user) }}" class="btn btn-sm btn-warning">Manage Permissions</a>
                                @endif
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $user)
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $user)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
@endsection
