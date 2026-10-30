@extends('layouts.guest')

@section('auth-title', 'Confirm Password')
@section('auth-heading', 'Confirm password')
@section('auth-subheading', 'Please confirm your password before continuing')

@section('content')
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror"
                   id="password" name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-login">Confirm</button>
    </form>
@endsection
