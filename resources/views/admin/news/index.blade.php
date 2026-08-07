@extends('layouts.app')

@section('page-title', 'News')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage university news articles.</p>
        @can('create', App\Models\News::class)
            <a href="{{ route('admin.news.create') }}" class="btn btn-primary">Add News</a>
        @endcan
    </div>

    <form method="GET" action="{{ route('admin.news.index') }}" class="row g-2 align-items-end mb-4">
        <div class="col-md-5">
            <label for="search" class="form-label">Search</label>
            <input type="search" name="search" id="search" class="form-control"
                   placeholder="Search by title or content..." value="{{ request('search') }}" maxlength="255">
        </div>
        <div class="col-md-3">
            <label for="department" class="form-label">Department</label>
            <select name="department" id="department" class="form-select">
                <option value="">All departments</option>
                <option value="{{ App\Models\News::MAIN_WEBSITE_SCOPE }}" @selected(request('department') === App\Models\News::MAIN_WEBSITE_SCOPE)>
                    Main Website
                </option>
                @foreach ($departments as $id => $name)
                    <option value="{{ $id }}" @selected((string) request('department') === (string) $id)>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-2">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
            @if (request()->filled('search') || request()->filled('department'))
                <a href="{{ route('admin.news.index') }}" class="btn btn-outline-secondary">Clear</a>
            @endif
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Department</th>
                        <th>Published Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($newsItems as $news)
                        <tr>
                            <td>{{ $news->title }}</td>
                            <td>{{ $news->departmentLabel() }}</td>
                            <td>{{ $news->published_date->format('d M Y') }}</td>
                            <td>
                                @if ($news->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.news.show', $news) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $news)
                                    <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $news)
                                    <form action="{{ route('admin.news.destroy', $news) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this news article?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No news articles found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $newsItems->links() }}
    </div>
@endsection
