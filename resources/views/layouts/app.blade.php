<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UAAR Admin') }} @isset($title) — {{ $title }} @endisset</title>

    <link rel="icon" href="{{ asset('images/admin/favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="admin-app">
    <div class="admin-shell">
        <nav class="admin-sidebar" id="admin-sidebar" aria-label="Admin navigation">
            <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__brand">
                <img src="{{ asset('images/admin/favicon.png') }}"
                     alt="{{ config('app.name') }}"
                     class="admin-sidebar__logo"
                     width="44"
                     height="44">
                <span>
                    <span class="admin-sidebar__brand-text">{{ config('app.name') }}</span>
                    <span class="admin-sidebar__brand-sub">Admin Panel</span>
                </span>
            </a>

            <ul class="admin-nav">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       class="admin-nav__link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                        <i class="bi bi-speedometer2" aria-hidden="true"></i>
                        Dashboard
                    </a>
                </li>

                @can('users.view')
                <li>
                    <a href="{{ route('admin.users.index') }}"
                       class="admin-nav__link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                        <i class="bi bi-people" aria-hidden="true"></i>
                        Users
                    </a>
                </li>
                @endcan

                @if (auth()->user()->isSuperAdmin())
                <li>
                    <a href="{{ route('admin.account.password.edit') }}"
                       class="admin-nav__link {{ request()->routeIs('admin.account.password.*') ? 'is-active' : '' }}">
                        <i class="bi bi-key" aria-hidden="true"></i>
                        Change Password
                    </a>
                </li>
                @endif

                <li class="admin-nav__label">Modules</li>
                @php
                    $moduleRoutes = [
                        'galleries' => ['route' => 'admin.galleries.index', 'icon' => 'bi-images'],
                        'news' => ['route' => 'admin.news.index', 'icon' => 'bi-newspaper'],
                        'tenders' => ['route' => 'admin.tenders.index', 'icon' => 'bi-file-earmark-text'],
                        'campus_publications' => ['route' => 'admin.campus-publications.index', 'icon' => 'bi-journal-bookmark'],
                        'jobs' => ['route' => 'admin.jobs.index', 'icon' => 'bi-briefcase'],
                        'departments' => ['route' => 'admin.departments.index', 'icon' => 'bi-building'],
                        'designations' => ['route' => 'admin.designations.index', 'icon' => 'bi-award'],
                        'faculties' => ['route' => 'admin.faculties.index', 'icon' => 'bi-mortarboard'],
                        'faculty_members' => ['route' => 'admin.faculty-members.index', 'icon' => 'bi-person-badge'],
                        'staff_members' => ['route' => 'admin.staff-members.index', 'icon' => 'bi-person-workspace'],
                        'events' => ['route' => 'admin.events.index', 'icon' => 'bi-calendar-event'],
                        'registration_fields' => ['route' => 'admin.registration-fields.index', 'icon' => 'bi-input-cursor-text'],
                        'event_registrations' => ['route' => 'admin.event-registrations.events-index', 'icon' => 'bi-clipboard-check'],
                    ];
                @endphp
                @foreach (config('modules') as $slug => $label)
                    @if ($slug === 'newsprint')
                        @continue
                    @endif
                    @php
                        $showModule = auth()->user()->can("{$slug}.view");
                        if ($slug === 'event_registrations') {
                            $showModule = $showModule
                                || auth()->user()->canAccessEventRegistrationsModule();
                        }
                    @endphp
                    @if ($showModule)
                        <li>
                            @if (isset($moduleRoutes[$slug]))
                                @php $routePrefix = str_replace('_', '-', $slug); @endphp
                                <a href="{{ route($moduleRoutes[$slug]['route']) }}"
                                   class="admin-nav__link {{ request()->routeIs('admin.'.$routePrefix.'.*') ? 'is-active' : '' }}">
                                    <i class="bi {{ $moduleRoutes[$slug]['icon'] }}" aria-hidden="true"></i>
                                    {{ $label }}
                                </a>
                            @else
                                <span class="admin-nav__link admin-nav__link--muted">
                                    <i class="bi bi-grid" aria-hidden="true"></i>
                                    {{ $label }}
                                </span>
                            @endif
                        </li>
                    @endif
                @endforeach

                @can('newsprint.view')
                <li class="admin-nav__label">Media Coverage</li>
                <li>
                    <a href="{{ route('admin.newsprint-albums.index') }}"
                       class="admin-nav__link {{ request()->routeIs('admin.newsprint-albums.*') || request()->routeIs('admin.newspapers.*') ? 'is-active' : '' }}">
                        <i class="bi bi-printer" aria-hidden="true"></i>
                        Print
                    </a>
                </li>
                @endcan
            </ul>
        </nav>

        <div class="admin-overlay" id="admin-overlay" aria-hidden="true"></div>

        <div class="admin-main">
            @php
                $hour = (int) now()->format('G');
                $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
                $initials = collect(explode(' ', auth()->user()->name))
                    ->filter()
                    ->take(2)
                    ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                    ->join('');
            @endphp
            <header class="admin-header">
                <div class="admin-header__left">
                    <button type="button" class="admin-header__menu-btn" id="admin-menu-btn" aria-label="Open menu">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <p class="admin-header__sub">{{ $greeting }}</p>
                        <h1 class="admin-header__greeting">@yield('page-title', 'Dashboard')</h1>
                    </div>
                </div>
                <div class="admin-header__right">
                    <div class="admin-user-pill">
                        <span class="admin-user-pill__avatar" aria-hidden="true">{{ $initials ?: 'U' }}</span>
                        <span class="admin-user-pill__name">{{ auth()->user()->name }}</span>
                    </div>
                    @if (auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.account.password.edit') }}" class="btn btn-outline-primary btn-sm">Change password</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Logout</button>
                    </form>
                </div>
            </header>

            <main class="admin-content">
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
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
