@extends('layouts.app')

@section('page-title', 'Manage Awards')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.faculty-members.index') }}" class="btn btn-outline-secondary btn-sm">← Back to Faculty Members</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h6 mb-1">{{ $facultyMember->name }}</h2>
            <p class="text-muted mb-0">{{ $facultyMember->designation?->name ?? '—' }} — {{ $facultyMember->department?->name }}</p>
        </div>
    </div>

    @can('update', $facultyMember)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h2 class="h6 mb-0">{{ $editingAward ? 'Edit Award' : 'Add Award' }}</h2>
            </div>
            <div class="card-body">
                <form method="POST"
                      action="{{ $editingAward
                          ? route('admin.faculty-members.awards.update', [$facultyMember, $editingAward])
                          : route('admin.faculty-members.awards.store', $facultyMember) }}">
                    @csrf
                    @if ($editingAward)
                        @method('PUT')
                    @endif

                    <div class="row g-3">
                        <div class="col-md-9">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror"
                                      required maxlength="2000">{{ old('description', $editingAward->description ?? '') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-3">
                            <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                            <input type="number" name="year" id="year" min="1900" max="{{ date('Y') + 1 }}"
                                   class="form-control @error('year') is-invalid @enderror"
                                   value="{{ old('year', $editingAward->year ?? date('Y')) }}" required>
                            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            {{ $editingAward ? 'Update Award' : 'Add Award' }}
                        </button>
                        @if ($editingAward)
                            <a href="{{ route('admin.faculty-members.awards.index', $facultyMember) }}" class="btn btn-outline-secondary">Cancel Edit</a>
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
                    @forelse ($awards as $award)
                        <tr>
                            <td>{{ $award->year }}</td>
                            <td>{{ $award->description }}</td>
                            <td class="text-end">
                                @can('update', $facultyMember)
                                    <a href="{{ route('admin.faculty-members.awards.index', [$facultyMember, 'edit' => $award->id]) }}"
                                       class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form action="{{ route('admin.faculty-members.awards.destroy', [$facultyMember, $award]) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this award?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No awards found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $awards->links() }}
    </div>
@endsection
