@extends('layouts.app')

@section('page-title', 'Events')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage university events and registration links.</p>
        @can('create', App\Models\Event::class)
            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">Add Event</a>
        @endcan
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title or event number...">
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach (['draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="department" class="form-select">
                <option value="">All departments</option>
                @foreach ($departments as $id => $name)
                    <option value="{{ $id }}" @selected((string) request('department') === (string) $id)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Event No.</th>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Registrations</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $event)
                        <tr>
                            <td>{{ $event->event_number }}</td>
                            <td>{{ $event->title }}</td>
                            <td>{{ $event->department?->name ?? '—' }}</td>
                            <td><span class="badge text-bg-secondary">{{ $event->statusLabel() }}</span></td>
                            <td>{{ $event->registrations_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $event)
                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No events found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $events->links() }}</div>
@endsection
