@extends('layouts.app')

@section('page-title', 'Manage Faculty Order')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.faculty-members.index') }}" class="btn btn-outline-secondary btn-sm">← Back to Faculty Members</a>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.faculty-members.order.index') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="faculty_id" class="form-label">Faculty <span class="text-danger">*</span></label>
                    <select name="faculty_id" id="faculty_id" class="form-select" required>
                        <option value="">Select faculty</option>
                        @foreach ($faculties as $id => $name)
                            <option value="{{ $id }}" @selected((string) $selectedFacultyId === (string) $id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                    <select name="department_id" id="department_id" class="form-select" required>
                        <option value="">Select department</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Load Members</button>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedFacultyId && $selectedDepartmentId)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Drag to reorder faculty members</h2>
                @if ($members->isNotEmpty() && auth()->user()->can('faculty_members.edit'))
                    <button type="submit" form="save-order-form" class="btn btn-primary btn-sm">Save Order</button>
                @endif
            </div>
            <div class="card-body">
                @if ($members->isEmpty())
                    <p class="text-muted mb-0">No faculty members found for this faculty and department.</p>
                @else
                    @if (auth()->user()->can('faculty_members.edit'))
                        <form id="save-order-form" method="POST" action="{{ route('admin.faculty-members.order.update') }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="faculty_id" value="{{ $selectedFacultyId }}">
                            <input type="hidden" name="department_id" value="{{ $selectedDepartmentId }}">
                            <div id="order-inputs"></div>
                        </form>
                    @endif

                    <ul id="sortable-members" class="list-group">
                        @foreach ($members as $member)
                            <li class="list-group-item d-flex align-items-center gap-3" data-id="{{ $member->id }}">
                                <span class="text-muted" style="cursor: grab;">☰</span>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $member->name }}</div>
                                    <small class="text-muted">
                                        {{ $member->designation ?? '—' }} — {{ $member->department?->name ?? '—' }}
                                    </small>
                                </div>
                                <span class="badge bg-light text-dark border">Order: {{ $member->sort_order }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif
@endsection

@include('admin.members._faculty-department-script')

@push('scripts')
    @if ($members->isNotEmpty() && auth()->user()->can('faculty_members.edit'))
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
        <script>
            (function () {
                const list = document.getElementById('sortable-members');
                const form = document.getElementById('save-order-form');
                const inputs = document.getElementById('order-inputs');

                if (!list || !form || !inputs) {
                    return;
                }

                new Sortable(list, {
                    animation: 150,
                    handle: '.text-muted',
                });

                form.addEventListener('submit', function () {
                    inputs.innerHTML = '';
                    list.querySelectorAll('[data-id]').forEach(function (item) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'order[]';
                        input.value = item.dataset.id;
                        inputs.appendChild(input);
                    });
                });
            })();
        </script>
    @endif
@endpush
