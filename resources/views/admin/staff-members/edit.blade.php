@extends('layouts.app')

@section('page-title', 'Edit Staff Member')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.staff-members.update', $member) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.members._form')

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Staff Member</button>
                    <a href="{{ route('admin.staff-members.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
