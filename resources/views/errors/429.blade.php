@extends('layouts.guest')

@section('auth-title', 'Too many attempts')
@section('auth-heading', 'Too many attempts')
@section('auth-subheading', 'Please wait a moment before trying again.')

@section('content')
    @php
        $retryAfterSeconds = null;

        if (isset($exception) && $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
            $headers = $exception->getHeaders();
            if (isset($headers['Retry-After'])) {
                $retryAfterSeconds = (int) $headers['Retry-After'];
            }
        }
    @endphp

    <p class="admin-panel-card__text mb-3">
        Too many attempts. Please wait a minute and try again.
    </p>

    @if ($retryAfterSeconds !== null && $retryAfterSeconds > 0)
        <p class="admin-panel-card__meta mb-4" id="retry-after-message">
            You can try again in <strong id="retry-after-seconds">{{ $retryAfterSeconds }}</strong>
            {{ $retryAfterSeconds === 1 ? 'second' : 'seconds' }}.
        </p>
    @else
        <p class="admin-panel-card__meta mb-4" id="retry-after-message" hidden></p>
    @endif

    <a href="{{ route('login') }}" class="btn-login d-inline-block text-center text-decoration-none">Back to login</a>

    @if ($retryAfterSeconds !== null && $retryAfterSeconds > 0)
        @push('scripts')
            <script>
                (function () {
                    var remaining = {{ (int) $retryAfterSeconds }};
                    var secondsEl = document.getElementById('retry-after-seconds');
                    var messageEl = document.getElementById('retry-after-message');
                    if (!secondsEl || !messageEl) {
                        return;
                    }
                    var timer = setInterval(function () {
                        remaining -= 1;
                        if (remaining <= 0) {
                            clearInterval(timer);
                            messageEl.textContent = 'You can try again now.';
                            return;
                        }
                        secondsEl.textContent = String(remaining);
                        messageEl.lastChild.textContent = remaining === 1 ? ' second.' : ' seconds.';
                    }, 1000);
                })();
            </script>
        @endpush
    @endif
@endsection
