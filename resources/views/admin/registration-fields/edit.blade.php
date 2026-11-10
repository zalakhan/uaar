@extends('layouts.app')

@section('page-title', 'Edit registration field')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.registration-fields.update', $field) }}">
                @csrf
                @method('PUT')
                @include('admin.registration-fields._form', ['field' => $field])
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('admin.registration-fields.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
