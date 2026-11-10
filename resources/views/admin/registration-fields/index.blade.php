@extends('layouts.app')

@section('page-title', 'Registration Field Library')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <p class="text-muted mb-0">Reusable fields for event registration forms.</p>
        @can('create', App\Models\RegistrationField::class)
            <a href="{{ route('admin.registration-fields.create') }}" class="btn btn-primary">Add field</a>
        @endcan
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Label</th>
                        <th>Key</th>
                        <th>Type</th>
                        <th>Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($fields as $field)
                        <tr>
                            <td>{{ $field->label }}</td>
                            <td><code>{{ $field->field_key }}</code></td>
                            <td>{{ $field->field_type }}</td>
                            <td>{{ $field->is_active ? 'Yes' : 'No' }}</td>
                            <td class="text-end">
                                @can('update', $field)
                                    <a href="{{ route('admin.registration-fields.edit', $field) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No fields in library.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $fields->links() }}</div>
@endsection
