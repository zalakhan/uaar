@extends('layouts.app')

@section('page-title', 'Print Albums')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage print media coverage albums.</p>
        <div class="d-flex gap-2">
            @can('viewAny', App\Models\Newspaper::class)
                <a href="{{ route('admin.newspapers.index') }}" class="btn btn-outline-secondary">Manage Newspapers</a>
            @endcan
            @can('create', App\Models\NewsprintAlbum::class)
                <a href="{{ route('admin.newsprint-albums.create') }}" class="btn btn-primary">Add Album</a>
            @endcan
        </div>
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.newsprint-albums.index'),
        'placeholder' => 'Search by album title...',
    ])

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.newsprint-albums.index') }}" class="row g-2 align-items-end">
                @if (request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                <div class="col-md-4">
                    <label for="status" class="form-label small mb-1">Status</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">All statuses</option>
                        <option value="1" @selected(request('status') === '1')>Active</option>
                        <option value="0" @selected(request('status') === '0')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                    <a href="{{ route('admin.newsprint-albums.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Publish Date</th>
                        <th>Clippings</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($albums as $album)
                        <tr>
                            <td>{{ $album->title }}</td>
                            <td>{{ $album->publish_date->format('d M Y') }}</td>
                            <td>{{ $album->news_prints_count }}</td>
                            <td>
                                @if ($album->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.newsprint-albums.show', $album) }}" class="btn btn-sm btn-outline-info">Manage Clippings</a>
                                @can('update', $album)
                                    <a href="{{ route('admin.newsprint-albums.edit', $album) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $album)
                                    <form action="{{ route('admin.newsprint-albums.destroy', $album) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this album and all its clippings?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No albums found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $albums->links() }}</div>
@endsection
