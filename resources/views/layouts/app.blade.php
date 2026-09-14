<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UAAR Admin') }} @isset($title) — {{ $title }} @endisset</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-light">
    <div class="d-flex" style="min-height: 100vh;">
        {{-- Sidebar --}}
        <nav class="bg-dark text-white p-3" style="width: 260px; min-height: 100vh;">
            <div class="mb-4">
                <a href="{{ route('admin.dashboard') }}" class="text-white text-decoration-none">
                    <h5 class="mb-0">{{ config('app.name') }}</h5>
                    <small class="text-secondary">Admin Panel</small>
                </a>
            </div>

            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link text-white {{ request()->routeIs('admin.dashboard') ? 'active bg-primary rounded' : '' }}">
                        Dashboard
                    </a>
                </li>

                @can('users.view')
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}"
                       class="nav-link text-white {{ request()->routeIs('admin.users.*') ? 'active bg-primary rounded' : '' }}">
                        Users
                    </a>
                </li>
                @endcan

                <li class="nav-item mt-3">
                    <span class="nav-link text-secondary small text-uppercase">Modules</span>
                </li>
                @php
                    $moduleRoutes = [
                        'galleries' => 'admin.galleries.index',
                        'news' => 'admin.news.index',
                        'tenders' => 'admin.tenders.index',
                        'campus_publications' => 'admin.campus-publications.index',
                        'jobs' => 'admin.jobs.index',
                        'departments' => 'admin.departments.index',
                        'designations' => 'admin.designations.index',
                        'faculties' => 'admin.faculties.index',
                        'faculty_members' => 'admin.faculty-members.index',
                        'staff_members' => 'admin.staff-members.index',
                    ];
                @endphp
                @foreach (config('modules') as $slug => $label)
                    @can("{$slug}.view")
                        <li class="nav-item">
                            @if (isset($moduleRoutes[$slug]))
                                @php $routePrefix = str_replace('_', '-', $slug); @endphp
                                <a href="{{ route($moduleRoutes[$slug]) }}"
                                   class="nav-link text-white {{ request()->routeIs('admin.'.$routePrefix.'.*') ? 'active bg-primary rounded' : '' }}">
                                    {{ $label }}
                                </a>
                            @else
                                <span class="nav-link text-white-50">{{ $label }}</span>
                            @endif
                        </li>
                    @endcan
                @endforeach
            </ul>
        </nav>

        {{-- Main content --}}
        <div class="flex-grow-1 d-flex flex-column">
            <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h5 mb-0">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                    </form>
                </div>
            </header>

            <main class="p-4 flex-grow-1">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
