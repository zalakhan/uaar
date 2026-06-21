@extends('layouts.app')

@section('page-title', 'Manage Permissions')

@section('content')
    {{-- User info header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-2">{{ $user->name }}</h5>
            <p class="mb-1">
                <strong>Role:</strong>
                <span class="badge bg-secondary">{{ $user->roles->pluck('name')->first() ?? '—' }}</span>
            </p>
            <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="mb-0 text-muted">
                <strong>Department:</strong> {{ $user->department?->name ?? '—' }}
            </p>
        </div>
    </div>

    <p class="text-muted mb-3">
        Check the actions this user is allowed to perform. Permissions are saved directly on the user account.
    </p>

    <form method="POST" action="{{ route('admin.users.permissions.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 200px;">Module</th>
                            <th class="text-center">View</th>
                            <th class="text-center">Create</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($modules as $slug => $label)
                            <tr>
                                <td class="fw-medium">{{ $label }}</td>
                                @foreach ($actions as $action)
                                    @php
                                        $permissionName = "{$slug}.{$action}";
                                        $isChecked = in_array($permissionName, old('permissions', $directPermissions), true);
                                    @endphp
                                    <td class="text-center">
                                        <input type="checkbox"
                                               class="form-check-input"
                                               name="permissions[]"
                                               value="{{ $permissionName }}"
                                               id="perm_{{ $slug }}_{{ $action }}"
                                               @checked($isChecked)>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @error('permissions')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
        @error('permissions.*')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Permissions</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
@endsection
