@extends('layouts.guest')

@section('content')
    <h4 class="mb-3">Forgot Password</h4>
    <p class="text-muted small mb-4">Enter your email and we will send you a password reset link.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">Email Reset Link</button>
            <a href="{{ route('login') }}" class="btn btn-link">Back to login</a>
        </div>
    </form>
@endsection
