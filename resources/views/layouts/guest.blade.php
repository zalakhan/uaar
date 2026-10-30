<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'UAAR Admin') }} — @yield('auth-title', 'Login')</title>

    <link rel="icon" href="{{ asset('images/admin/favicon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="admin-auth">
    <div class="admin-auth-layout">
        <aside class="admin-auth-hero" style="background-image: url('{{ asset('images/admin/admin-block.jpg') }}');" aria-hidden="true">
            <div class="admin-auth-hero__inner">
                <h1 class="admin-auth-hero__title">Welcome to PMAS-Arid Agriculture University Admin Portal</h1>
            </div>
        </aside>

        <main class="admin-auth-panel">
            <div class="admin-auth-card">
                <img src="{{ asset('images/admin/favicon.png') }}"
                     alt="{{ config('app.name') }}"
                     class="admin-auth-card__logo"
                     width="72"
                     height="72">
                <h2 class="admin-auth-card__heading">@yield('auth-heading', 'Welcome back')</h2>
                <p class="admin-auth-card__sub">@yield('auth-subheading', 'Sign in to continue to the admin panel')</p>

                @yield('content')
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
