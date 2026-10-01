@extends('layouts.app')

@section('page-title', 'Add Print Album')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.newsprint-albums.store') }}">
                @csrf
                @include('admin.newsprint-albums._form')
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Album</button>
                    <a href="{{ route('admin.newsprint-albums.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
