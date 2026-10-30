@extends('layouts.guest')

@section('auth-title', 'Forgot Password')
@section('auth-heading', 'Forgot password?')
@section('auth-subheading', 'Enter your email and we will send you a reset link')

@section('content')
    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
                   id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-login mb-3">Email reset link</button>
        <div class="text-center">
            <a href="{{ route('login') }}">Back to login</a>
        </div>
    </form>
@endsection
