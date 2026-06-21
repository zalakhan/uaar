@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Total Users</h6>
                    <p class="display-6 mb-0">{{ $userCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Welcome, {{ auth()->user()->name }}</h5>
                    <p class="text-muted mb-2">
                        Role:
                        <span class="badge bg-primary">{{ auth()->user()->roles->pluck('name')->join(', ') ?: 'No role' }}</span>
                    </p>
                    @if (auth()->user()->department)
                        <p class="text-muted mb-0">Department: {{ auth()->user()->department->name }}</p>
                    @endif
                    <hr>
                    <p class="mb-0 small text-muted">
                        Phase 0 is complete. Use the sidebar to manage users. More modules will be added in upcoming phases.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
