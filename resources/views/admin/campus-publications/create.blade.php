@extends('layouts.app')

@section('page-title', 'Add Campus Publication')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.campus-publications.store') }}" enctype="multipart/form-data">
                @csrf

                @include('admin.campus-publications._form')

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Publication</button>
                    <a href="{{ route('admin.campus-publications.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
