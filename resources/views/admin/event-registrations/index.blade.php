@extends('layouts.app')

@section('page-title', 'Registrations — '.$event->title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.event-registrations.events-index') }}" class="btn btn-sm btn-outline-secondary">&larr; All events</a>
        @can('event-registrations.export', $event)
            <a href="{{ route('admin.events.registrations.export', $event) }}" class="btn btn-sm btn-success">Export Excel</a>
        @endcan
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="search" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search registration no. or category...">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">All categories</option>
                @foreach ($categories as $id => $name)
                    <option value="{{ $id }}" @selected((string) request('category') === (string) $id)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-primary w-100" type="submit">Filter</button>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reg. No.</th>
                        <th>Category</th>
                        <th>Submitted</th>
                        @foreach ($event->formFields as $field)
                            <th>{{ $field->label }}</th>
                        @endforeach
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($registrations as $registration)
                        @php $answers = $registration->answerMap(); @endphp
                        <tr>
                            <td>{{ $registration->registration_number }}</td>
                            <td>{{ $registration->category_label }}</td>
                            <td>{{ $registration->submitted_at?->format('d M Y H:i') }}</td>
                            @foreach ($event->formFields as $field)
                                <td class="small">{{ \Illuminate\Support\Str::limit($answers[$field->field_key] ?? '—', 40) }}</td>
                            @endforeach
                            <td>
                                <a href="{{ route('admin.events.registrations.show', [$event, $registration]) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + $event->formFields->count() }}" class="text-center text-muted py-4">No registrations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $registrations->links() }}</div>
@endsection
