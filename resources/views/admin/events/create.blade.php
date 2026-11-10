@extends('layouts.app')

@section('page-title', 'Add Event')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.events.store') }}">
                @csrf
                @include('admin.events._form')
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Event</button>
                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
