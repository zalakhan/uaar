@extends('layouts.app')

@section('page-title', 'Add Newspaper')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.newspapers.store') }}">
                @csrf
                @include('admin.newspapers._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Newspaper</button>
                    <a href="{{ route('admin.newspapers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
