@extends('layouts.app')

@section('page-title', 'Event Registrations')

@section('content')
    <p class="text-muted">View and export registrations for events you are authorized to access.</p>

    @include('admin.partials.search-form', [
        'action' => route('admin.event-registrations.events-index'),
        'placeholder' => 'Search events...',
    ])

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Event</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Registrations</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>{{ $event->title }}<br><small class="text-muted">{{ $event->event_number }}</small></td>
                            <td>{{ $event->department?->name ?? '—' }}</td>
                            <td>{{ $event->statusLabel() }}</td>
                            <td>{{ $event->registrations_count }}</td>
                            <td class="text-end">
                                @can('event-registrations.view-event', $event)
                                    <a href="{{ route('admin.events.registrations.index', $event) }}" class="btn btn-sm btn-outline-primary">Open</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No accessible events.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $events->links() }}</div>
@endsection
