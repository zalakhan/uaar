@extends('layouts.app')

@section('page-title', 'Edit Campus Publication')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.campus-publications.update', $publication) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.campus-publications._form', ['publication' => $publication])

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Publication</button>
                    <a href="{{ route('admin.campus-publications.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
