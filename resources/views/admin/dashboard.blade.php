@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
    @php
        $roleName = auth()->user()->roles->pluck('name')->first() ?? 'No role';
        $isSuperAdmin = auth()->user()->isSuperAdmin();
        $visibleModules = 0;
        if ($isSuperAdmin) {
            foreach (config('modules') as $slug => $label) {
                if ($slug === 'newsprint') {
                    continue;
                }
                if (auth()->user()->can("{$slug}.view")) {
                    $visibleModules++;
                }
            }
            if (auth()->user()->can('newsprint.view')) {
                $visibleModules++;
            }
        }
    @endphp

    <div class="admin-stat-grid mb-4 {{ $isSuperAdmin ? '' : 'admin-stat-grid--compact' }}">
        @if ($isSuperAdmin)
            <div class="admin-stat-card admin-stat-card--accent-fern">
                <div class="admin-stat-card__label">Total users</div>
                <p class="admin-stat-card__value">{{ $userCount }}</p>
            </div>
            <div class="admin-stat-card admin-stat-card--accent-moss">
                <div class="admin-stat-card__label">Your modules</div>
                <p class="admin-stat-card__value">{{ $visibleModules }}</p>
            </div>
        @endif
        <div class="admin-stat-card admin-stat-card--accent-reseda">
            <div class="admin-stat-card__label">Your role</div>
            <p class="admin-stat-card__value admin-stat-card__value--text">{{ str_replace('_', ' ', $roleName) }}</p>
        </div>
        <div class="admin-stat-card admin-stat-card--accent-pakistan">
            <div class="admin-stat-card__label">Department</div>
            <p class="admin-stat-card__value admin-stat-card__value--text">{{ auth()->user()->department?->name ?? '—' }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-panel-card">
                <h2 class="admin-panel-card__title">Welcome, {{ auth()->user()->name }}</h2>
                <p class="admin-panel-card__text mb-0">
                    Use the sidebar to access the sections available to you. Your access
                    depends on your assigned role and permissions.
                </p>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="admin-panel-card admin-panel-card--tips">
                <h2 class="admin-panel-card__title">Quick tips</h2>
                <ul class="admin-panel-card__list mb-0 ps-3">
                    <li class="mb-2">Open a section from the sidebar to view or update content.</li>
                    <li class="mb-2">Some items are limited to your department or role.</li>
                    <li>Contact the web manager if you need additional permissions.</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
