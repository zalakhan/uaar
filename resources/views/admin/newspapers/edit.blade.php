@extends('layouts.app')

@section('page-title', 'Edit Newspaper')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.newspapers.update', $newspaper) }}">
                @csrf
                @method('PUT')
                @include('admin.newspapers._form')
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Newspaper</button>
                    <a href="{{ route('admin.newspapers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
