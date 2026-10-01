@extends('layouts.app')

@section('page-title', 'Edit Print Album')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.newsprint-albums.update', $album) }}">
                @csrf
                @method('PUT')
                @include('admin.newsprint-albums._form', ['album' => $album])
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Album</button>
                    <a href="{{ route('admin.newsprint-albums.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
