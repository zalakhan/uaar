<?php

namespace App\Http\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

trait ThrottlesGuestAuthRoutes
{
    /**
     * Limit guest auth POST actions (forgot / reset password).
     */
    protected function throttleGuestAuthRequest(Request $request, string $action, int $maxAttempts, string $field = 'email'): void
    {
        $key = $action.'|'.Str::transliterate(Str::lower((string) $request->input($field, '')).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            throw new TooManyRequestsHttpException(RateLimiter::availableIn($key));
        }

        RateLimiter::hit($key, 60);
    }
}
