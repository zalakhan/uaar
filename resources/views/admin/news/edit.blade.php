@extends('layouts.app')

@section('page-title', 'Edit News')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.news._form', ['news' => $news])

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update News</button>
                    <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/news-editor.js'])
    @endpush
@endsection
