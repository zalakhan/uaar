@extends('layouts.app')

@section('page-title', 'Tenders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Manage university tenders.</p>
        @can('create', App\Models\Tender::class)
            <a href="{{ route('admin.tenders.create') }}" class="btn btn-primary">Add Tender</a>
        @endcan
    </div>

    @include('admin.partials.search-form', [
        'action' => route('admin.tenders.index'),
        'placeholder' => 'Search by title, number, or category...',
    ])

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Tender No.</th>
                        <th>Due Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tenders as $tender)
                        <tr>
                            <td>{{ $tender->title ?? '—' }}</td>
                            <td>{{ $tender->categoryLabel() }}</td>
                            <td>{{ $tender->tender_no ?? '—' }}</td>
                            <td>{{ $tender->due_date?->format('d M Y') ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.tenders.show', $tender) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                @can('update', $tender)
                                    <a href="{{ route('admin.tenders.edit', $tender) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                                @can('delete', $tender)
                                    <form action="{{ route('admin.tenders.destroy', $tender) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this tender?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No tenders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $tenders->links() }}
    </div>
@endsection
