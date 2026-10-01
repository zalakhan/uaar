@extends('layouts.app')

@section('page-title', 'Manage Clippings')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h6 mb-1">{{ $album->title }}</h2>
            <p class="text-muted mb-0">Published {{ $album->publish_date->format('d M Y') }}</p>
        </div>
        <a href="{{ route('admin.newsprint-albums.index') }}" class="btn btn-outline-secondary btn-sm">← Back to Albums</a>
    </div>

    @can('update', $album)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h2 class="h6 mb-0">Add Clippings</h2>
            </div>
            <div class="card-body">
                @if ($newspapers->isEmpty())
                    <div class="alert alert-warning mb-0">
                        Add at least one <a href="{{ route('admin.newspapers.create') }}">newspaper</a> before uploading clippings.
                    </div>
                @else
                    <form method="POST" action="{{ route('admin.newsprint-albums.clippings.store', $album) }}" enctype="multipart/form-data" id="clippings-form">
                        @csrf
                        <div id="clipping-rows" class="vstack gap-3">
                            <div class="row g-3 clipping-row align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">Image <span class="text-danger">*</span></label>
                                    <input type="file" name="clippings[0][news_file]" class="form-control @error('clippings.0.news_file') is-invalid @enderror"
                                           accept=".jpg,.jpeg,.png" required>
                                    @error('clippings.0.news_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Newspaper <span class="text-danger">*</span></label>
                                    <select name="clippings[0][newspaper_id]" class="form-select @error('clippings.0.newspaper_id') is-invalid @enderror" required>
                                        <option value="">Select newspaper</option>
                                        @foreach ($newspapers as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @error('clippings.0.newspaper_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-outline-danger w-100 remove-row d-none">Remove</button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" id="add-clipping-row">Add Another</button>
                            <button type="submit" class="btn btn-primary">Upload Clippings</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endcan

    <div class="row g-3">
        @forelse ($album->newsPrints as $clipping)
            <div class="col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm h-100">
                    <img src="{{ $clipping->fileUrl() }}" class="card-img-top" alt="Clipping" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <p class="small text-muted mb-2">{{ $clipping->newspaper?->newspaper_name ?? '—' }}</p>

                        @can('update', $album)
                            <form method="POST" action="{{ route('admin.newsprint-albums.clippings.update', [$album, $clipping]) }}" enctype="multipart/form-data" class="mb-2">
                                @csrf
                                @method('PUT')
                                <div class="mb-2">
                                    <label class="form-label small">Newspaper</label>
                                    <select name="newspaper_id" class="form-select form-select-sm" required>
                                        @foreach ($newspapers as $id => $name)
                                            <option value="{{ $id }}" @selected($clipping->newspaper_id == $id)>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small">Replace Image</label>
                                    <input type="file" name="news_file" class="form-control form-control-sm" accept=".jpg,.jpeg,.png">
                                    <div class="form-text">Leave empty to keep current image.</div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                            </form>
                            <form action="{{ route('admin.newsprint-albums.clippings.destroy', [$album, $clipping]) }}" method="POST"
                                  onsubmit="return confirm('Delete this clipping?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-secondary mb-0">No clippings in this album yet.</div>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('clipping-rows');
    const addBtn = document.getElementById('add-clipping-row');

    if (!container || !addBtn) return;

    let rowIndex = container.querySelectorAll('.clipping-row').length;

    const newspaperOptions = @json(
        $newspapers->map(fn ($name, $id) => ['id' => $id, 'name' => $name])->values()->all()
    );

    addBtn.addEventListener('click', () => {
        const row = document.createElement('div');
        row.className = 'row g-3 clipping-row align-items-end';
        row.innerHTML = `
            <div class="col-md-5">
                <label class="form-label">Image <span class="text-danger">*</span></label>
                <input type="file" name="clippings[${rowIndex}][news_file]" class="form-control" accept=".jpg,.jpeg,.png" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Newspaper <span class="text-danger">*</span></label>
                <select name="clippings[${rowIndex}][newspaper_id]" class="form-select" required>
                    <option value="">Select newspaper</option>
                    ${newspaperOptions.map(o => `<option value="${o.id}">${o.name}</option>`).join('')}
                </select>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger w-100 remove-row">Remove</button>
            </div>
        `;
        container.appendChild(row);
        rowIndex++;
        updateRemoveButtons();
    });

    container.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('.clipping-row').remove();
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const rows = container.querySelectorAll('.clipping-row');
        rows.forEach((row, index) => {
            const btn = row.querySelector('.remove-row');
            if (btn) btn.classList.toggle('d-none', rows.length === 1);
        });
    }

    updateRemoveButtons();
});
</script>
@endpush
