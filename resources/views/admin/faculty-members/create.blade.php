@extends('layouts.app')

@section('page-title', 'Add Faculty Member')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.faculty-members.store') }}" enctype="multipart/form-data">
                @csrf

                @include('admin.members._form', ['requireFacultyFields' => true, 'hideSortOrder' => true])

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Faculty Member</button>
                    <a href="{{ route('admin.faculty-members.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/faculty-member-qualification-editor.js'])
    @endpush
@endsection
