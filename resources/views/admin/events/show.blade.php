@extends('layouts.app')

@section('page-title', $event->title)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <p class="text-muted mb-1">{{ $event->event_number }}</p>
            <span class="badge text-bg-secondary">{{ $event->statusLabel() }}</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @can('update', $event)
                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-primary btn-sm">Edit details</a>
                <a href="{{ route('admin.events.form-builder.edit', $event) }}" class="btn btn-primary btn-sm">Form builder</a>
            @endcan
            @can('event-registrations.view-event', $event)
                <a href="{{ route('admin.events.registrations.index', $event) }}" class="btn btn-outline-secondary btn-sm">Registrations</a>
            @endcan
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h5">Event information</h2>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Department</dt>
                        <dd class="col-sm-8">{{ $event->department?->name ?? '—' }}</dd>
                        <dt class="col-sm-4">Event date</dt>
                        <dd class="col-sm-8">{{ $event->event_date?->format('d M Y') ?? '—' }}</dd>
                        <dt class="col-sm-4">Deadline</dt>
                        <dd class="col-sm-8">{{ $event->registration_deadline?->format('d M Y H:i') ?? '—' }}</dd>
                        <dt class="col-sm-4">Venue</dt>
                        <dd class="col-sm-8">{{ $event->venue ?? '—' }}</dd>
                        <dt class="col-sm-4">Type</dt>
                        <dd class="col-sm-8">{{ $event->event_type ?? '—' }}</dd>
                    </dl>
                    @if ($event->description)
                        <hr>
                        <p class="mb-0">{!! nl2br(e($event->description)) !!}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6">Public registration link</h2>
                    <p class="small text-muted">Share this URL with participants (no login required).</p>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" class="form-control" readonly value="{{ $event->publicRegistrationUrl() }}" id="public-url">
                        <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('public-url').value)">Copy</button>
                    </div>
                    @can('update', $event)
                        <div class="d-flex flex-column gap-2">
                            @if ($event->status !== App\Models\Event::STATUS_PUBLISHED)
                                <form method="POST" action="{{ route('admin.events.publish', $event) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm w-100">Publish event</button>
                                </form>
                            @endif
                            @if ($event->status === App\Models\Event::STATUS_PUBLISHED)
                                <form method="POST" action="{{ route('admin.events.close', $event) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm w-100">Close registration</button>
                                </form>
                            @endif
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6">Participant categories ({{ $event->participantCategories->count() }})</h2>
                    <ul class="mb-0">
                        @forelse ($event->participantCategories as $category)
                            <li>{{ $category->name }}</li>
                        @empty
                            <li class="text-muted">Not configured — use the form builder.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h2 class="h6">Form fields ({{ $event->formFields->count() }})</h2>
                    <p class="small text-muted mb-2">
                        Fees: {{ $event->fees_enabled ? 'Enabled' : 'Disabled' }}
                    </p>
                    <a href="{{ route('admin.events.form-preview', $event) }}" class="btn btn-sm btn-outline-primary" target="_blank">Live preview</a>
                </div>
            </div>
        </div>
    </div>

    @if (auth()->user()->isSuperAdmin())
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h2 class="h5">Who can view registrations</h2>
                <p class="text-muted small mb-0">
                    Super Admin can view all events. For this event, only <strong>department admins</strong>
                    (role <code>dept_admin</code>) whose account department matches the
                    <strong>organizing department</strong>
                    ({{ $event->department?->name ?? 'not set — assign a department on this event' }}) can view and export registrations.
                    Staff and department admins from other departments cannot access this data.
                </p>
            </div>
        </div>
    @endif
@endsection
