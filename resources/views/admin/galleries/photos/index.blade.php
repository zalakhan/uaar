@extends('layouts.app')

@section('page-title', 'Gallery Photos')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-0">{{ $gallery->name }} — {{ $gallery->department?->name ?? 'No department' }}</p>
            @can('update', $gallery)
                @if ($photos->isNotEmpty())
                    <small class="text-muted">Drag photos to reorder. Changes save automatically.</small>
                @endif
            @endcan
        </div>
        <a href="{{ route('admin.galleries.show', $gallery) }}" class="btn btn-outline-secondary">Back to Gallery</a>
    </div>

    @can('update', $gallery)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6 mb-3">Upload Photos</h2>
                <form method="POST" action="{{ route('admin.galleries.photos.store', $gallery) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label for="photos" class="form-label">Select Images</label>
                            <input type="file" name="photos[]" id="photos" class="form-control @error('photos') is-invalid @enderror @error('photos.*') is-invalid @enderror"
                                   accept="image/*" multiple required>
                            @error('photos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @error('photos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan

    <div class="row g-3" id="sortable-photos">
        @forelse ($photos as $photo)
            <div class="col-md-3 col-sm-6" data-id="{{ $photo->id }}">
                <div class="card border-0 shadow-sm h-100 gallery-photo-card">
                    @can('update', $gallery)
                        <div class="drag-handle text-muted px-2 py-1 border-bottom bg-light" title="Drag to reorder">
                            <span aria-hidden="true">⋮⋮</span> Drag
                        </div>
                    @endcan
                    <img src="{{ asset('storage/'.$photo->photo) }}" class="card-img-top" alt="Gallery photo" style="height: 180px; object-fit: cover;">
                    @can('update', $gallery)
                        <div class="card-body p-2 text-end">
                            <form action="{{ route('admin.galleries.photos.destroy', [$gallery, $photo]) }}" method="POST"
                                  onsubmit="return confirm('Delete this photo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-secondary mb-0">No photos in this gallery yet.</div>
            </div>
        @endforelse
    </div>
@endsection

@push('styles')
    <style>
        .gallery-photo-card {
            transition: box-shadow 0.15s ease, transform 0.15s ease;
        }

        .gallery-photo-card.sortable-chosen {
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.18);
            transform: scale(1.02);
        }

        .gallery-photo-card.sortable-ghost {
            opacity: 0.45;
        }

        .drag-handle {
            cursor: grab;
            user-select: none;
            font-size: 0.85rem;
        }

        .drag-handle:active {
            cursor: grabbing;
        }
    </style>
@endpush

@if ($photos->isNotEmpty() && auth()->user()->can('update', $gallery))
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            (function () {
                const list = document.getElementById('sortable-photos');
                const orderUrl = @json(route('admin.galleries.photos.order.update', $gallery));
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                if (!list || !orderUrl || !csrfToken) {
                    return;
                }

                let saveTimeout = null;

                function saveOrder() {
                    const order = Array.from(list.querySelectorAll('[data-id]')).map(function (item) {
                        return Number(item.dataset.id);
                    });

                    fetch(orderUrl, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ order: order }),
                    }).catch(function () {
                        alert('Could not save photo order. Please refresh and try again.');
                    });
                }

                new Sortable(list, {
                    animation: 150,
                    handle: '.drag-handle',
                    draggable: '[data-id]',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function () {
                        clearTimeout(saveTimeout);
                        saveTimeout = setTimeout(saveOrder, 250);
                    },
                });
            })();
        </script>
    @endpush
@endif
