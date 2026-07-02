@extends('layouts.app')

@section('page-title', 'Staff Member Details')

@section('content')
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if ($member->photo)
                <div class="mb-4">
                    <img src="{{ asset('storage/'.$member->photo) }}" alt="{{ $member->name }}" class="rounded" style="max-height: 150px;">
                </div>
            @endif

            <dl class="row mb-0">
                <dt class="col-sm-3">Name</dt>
                <dd class="col-sm-9">{{ $member->name }}</dd>

                <dt class="col-sm-3">Designation</dt>
                <dd class="col-sm-9">{{ $member->designation?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Department</dt>
                <dd class="col-sm-9">{{ $member->department?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Faculty</dt>
                <dd class="col-sm-9">{{ $member->faculty?->name ?? '—' }}</dd>

                <dt class="col-sm-3">Email</dt>
                <dd class="col-sm-9">{{ $member->email ?? '—' }}</dd>

                <dt class="col-sm-3">Phone</dt>
                <dd class="col-sm-9">{{ $member->phone ?? '—' }}</dd>

                <dt class="col-sm-3">Mobile</dt>
                <dd class="col-sm-9">{{ $member->mobile ?? '—' }}</dd>

                <dt class="col-sm-3">Qualification</dt>
                <dd class="col-sm-9">{{ $member->qualification ?? '—' }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    @if ($member->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                    @if ($member->is_onleave)<span class="badge bg-warning text-dark">On Leave</span>@endif
                </dd>

                @if ($member->bio)
                    <dt class="col-sm-3">Bio</dt>
                    <dd class="col-sm-9">{{ $member->bio }}</dd>
                @endif
            </dl>
        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        @can('update', $member)
            <a href="{{ route('admin.staff-members.edit', $member) }}" class="btn btn-primary">Edit</a>
        @endcan
        <a href="{{ route('admin.staff-members.index') }}" class="btn btn-outline-secondary">Back to list</a>
    </div>
@endsection
