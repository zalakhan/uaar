@extends('layouts.app')

@section('page-title', 'User Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $user->name }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $user->email }}</dd>

                <dt class="col-sm-3">Role</dt>
                <dd class="col-sm-9">
                    <span class="badge bg-secondary">{{ $user->roles->pluck('name')->first() ?? '—' }}</span>
                </dd>

                <dt class="col-sm-3">Department</dt>
                <dd class="col-sm-9">{{ $user->department?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Created</dt>
                <dd class="col-sm-9">{{ $user->created_at->format('d M Y, h:i A') }}</dd>
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        @if (auth()->user()->isSuperAdmin() && ! $user->isSuperAdmin())
            <a href="{{ route('admin.users.permissions.edit', $user) }}" class="btn btn-warning">Manage Permissions</a>
        @endif
        @can('update', $user)
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection
