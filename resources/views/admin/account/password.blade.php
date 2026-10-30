@extends('layouts.app')

@section('page-title', 'Change Password')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="admin-panel-card">
                <h2 class="admin-panel-card__title">Change your password</h2>
                <p class="admin-panel-card__text mb-4">
                    Update the password for your super admin account. You will use the new password the next time you sign in.
                </p>

                <form method="POST" action="{{ route('admin.account.password.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current password</label>
                        <input type="password" name="current_password" id="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               required autocomplete="current-password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New password</label>
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Confirm new password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary">Update password</button>
                </form>
            </div>
        </div>
    </div>
@endsection
