@extends('layouts.app')

@section('page-title', 'Add News')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
                @csrf

                @include('admin.news._form')

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create News</button>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/news-editor.js'])
    @endpush
@endsection
