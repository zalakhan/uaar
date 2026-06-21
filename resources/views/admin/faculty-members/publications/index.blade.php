@extends('layouts.app')

@section('page-title', 'Manage Publications')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.faculty-members.index') }}" class="btn btn-outline-secondary btn-sm">← Back to Faculty Members</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h6 mb-1">{{ $facultyMember->name }}</h2>
            <p class="text-muted mb-0">{{ $facultyMember->designation }} — {{ $facultyMember->department?->name }}</p>
        </div>
    </div>

    @can('update', $facultyMember)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h2 class="h6 mb-0">{{ $editingPublication ? 'Edit Publication' : 'Add Publication' }}</h2>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ $editingPublication
                          ? route('admin.faculty-members.publications.update', [$facultyMember, $editingPublication])
                          : route('admin.faculty-members.publications.store', $facultyMember) }}">
                    @csrf
                    @if ($editingPublication)
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-9">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                                      required maxlength="2000">{{ old('description', $editingPublication->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" id="year" min="1900" max="{{ date('Y') + 1 }}"
                                   class="form-control @error('year') is-invalid @enderror"
                                   value="{{ old('year', $editingPublication->year ?? date('Y')) }}" required>
                            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ $editingPublication ? 'Update Publication' : 'Add Publication' }}
                        </button>
                        @if ($editingPublication)
                            <a href="{{ route('admin.faculty-members.publications.index', $facultyMember) }}" class="btn btn-outline-secondary">Cancel Edit</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Year</th>
                        <th>Description</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($publications as $publication)
                        <tr>
                            <td>{{ $publication->year }}</td>
                            <td>{{ $publication->description }}</td>
                            <td class="text-end">
                                @can('update', $facultyMember)
                                    <a href="{{ route('admin.faculty-members.publications.index', [$facultyMember, 'edit' => $publication->id]) }}"
                                       class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.faculty-members.publications.destroy', [$facultyMember, $publication]) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this publication?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No publications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $publications->links() }}
    </div>
@endsection
