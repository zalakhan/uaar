<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Online Services') — {{ config('app.name', 'UAAR') }}</title>

    <link rel="icon" href="{{ asset('images/uaar-logo.png') }}" type="image/png">
    <link href="{{ asset('css/public.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="public-body">
    <header class="public-header">
        <div class="public-header__inner">
            <a href="https://uaar.edu.pk/" class="public-header__brand" rel="noopener noreferrer">
                <img src="{{ asset('images/uaar-logo-text.png') }}"
                     alt="PMAS-Arid Agriculture University Rawalpindi"
                     class="public-header__logo"
                     width="384"
                     height="100">
            </a>
            <a href="https://uaar.edu.pk/" class="public-btn public-btn--outline" rel="noopener noreferrer">
                Back to Website
            </a>
        </div>
    </header>

    <main class="public-main">
        @yield('content')
    </main>

    <footer class="public-footer">
        &copy; {{ date('Y') }} Pir Mehr Ali Shah Arid Agriculture University Rawalpindi. All rights reserved.
    </footer>

    @stack('scripts')
</body>
</html>
