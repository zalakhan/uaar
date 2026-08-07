@extends('layouts.app')

@section('page-title', 'Edit Tender')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.tenders.update', $tender) }}" enctype="multipart/form-data" id="tender-form">
                @csrf
                @method('PUT')

                @include('admin.tenders._form', ['tender' => $tender])

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Tender</button>
                    <a href="{{ route('admin.tenders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/tender-form.js'])
    @endpush
@endsection
