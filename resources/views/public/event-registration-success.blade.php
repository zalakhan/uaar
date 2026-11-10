@extends('layouts.public')

@section('title', 'Registration confirmed')

@section('content')
    <div class="public-card">
        <aside class="public-card__aside" aria-label="Registration complete">
            <h2 class="public-card__aside-title">Thank you</h2>
            <p class="public-card__aside-text">
                Your registration has been received. Please save your registration number for future reference.
            </p>
            <ul class="public-card__aside-steps">
                <li><span class="public-card__step-dot" aria-hidden="true"></span> Category selected</li>
                <li><span class="public-card__step-dot" aria-hidden="true"></span> Details submitted</li>
                <li><span class="public-card__step-dot is-active" aria-hidden="true"></span> Registration number issued</li>
            </ul>
        </aside>

        <div class="public-card__body">
            @if (session('already_registered'))
                <h2 class="public-card__heading">Already registered</h2>
                <p class="public-card__subtitle">You have already registered for <strong>{{ $event->title }}</strong>.</p>
                <x-public.alert type="warning">Yes — we found an existing registration for this event using your email, CNIC, or mobile number. No new registration was created.</x-public.alert>
            @else
                <h2 class="public-card__heading">Registration successful</h2>
                <p class="public-card__subtitle">You are registered for <strong>{{ $event->title }}</strong>.</p>
                <x-public.alert type="success">Your registration has been confirmed.</x-public.alert>
            @endif

            <p class="public-label" style="margin-top: 1.25rem;">Registration number</p>
            <p class="public-registration-number">{{ $record->registration_number }}</p>
            <p class="public-card__subtitle">Event reference: {{ $event->event_number }}</p>
        </div>
    </div>
@endsection
