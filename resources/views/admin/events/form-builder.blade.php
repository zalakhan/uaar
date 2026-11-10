@extends('layouts.app')

@section('page-title', 'Form Builder — '.$event->title)

@section('content')
    <p class="text-muted">Configure participant categories, form fields, conditional rules, and optional fees.</p>

    <form method="POST" action="{{ route('admin.events.form-builder.update', $event) }}" id="form-builder">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h6">Participant categories</h2>
                <p class="small text-muted">Add as many categories as this event needs (students, faculty, external, etc.).</p>
                <div id="categories-list" class="vstack gap-2 mb-2"></div>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-category">Add category</button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h2 class="h6 mb-0">Form fields</h2>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#libraryModal">Add from library</button>
                </div>
                <p class="small text-muted">Drag rows to reorder. Configure per-category visibility and required rules below each field.</p>
                <div id="fields-list" class="vstack gap-3"></div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-custom-field">Add custom field</button>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="fees_enabled" value="1" id="fees_enabled"
                           @checked(old('fees_enabled', $event->fees_enabled))>
                    <label class="form-check-label" for="fees_enabled">Enable registration fees</label>
                </div>
                <div id="fee-rules" class="row g-2"></div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save configuration</button>
            <a href="{{ route('admin.events.form-preview', $event) }}" class="btn btn-outline-secondary" target="_blank">Preview form</a>
            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-secondary">Back to event</a>
        </div>
    </form>

    <div class="modal fade" id="libraryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h5">Field library</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group">
                        @foreach ($libraryFields as $lib)
                            <button type="button" class="list-group-item list-group-item-action library-pick"
                                    data-id="{{ $lib->id }}"
                                    data-field-key="{{ $lib->field_key }}"
                                    data-label="{{ $lib->label }}"
                                    data-field-type="{{ $lib->field_type }}"
                                    data-options="{{ implode('|', $lib->options ?? []) }}">
                                <strong>{{ $lib->label }}</strong>
                                <span class="text-muted small">({{ $lib->field_type }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        const initialCategories = @json($event->participantCategories->values());
        const initialFields = @json($event->formFields->load('conditions')->values());
        const initialFees = @json($event->feeRules->keyBy('event_participant_category_id'));
        const linkedFieldKeys = ['faculty', 'department'];

        const fieldTypes = {
            text: 'Text',
            textarea: 'Textarea',
            email: 'Email',
            tel: 'Phone',
            date: 'Date',
            select: 'Select',
            file: 'File',
        };

        let categories = initialCategories.length
            ? initialCategories.map(c => ({ id: c.id, name: c.name }))
            : [{ id: null, name: 'General' }];

        let fields = initialFields.map(f => ({
            id: f.id,
            registration_field_id: f.registration_field_id,
            field_key: f.field_key,
            label: f.label,
            field_type: f.field_type,
            placeholder: f.placeholder || '',
            is_required: f.is_required,
            options_text: (f.options || []).join('\n'),
            conditions: (f.conditions || []).map(cond => ({
                category_index: categories.findIndex(cat => cat.id === cond.event_participant_category_id),
                is_visible: cond.is_visible,
                is_required: cond.is_required,
            })),
        }));

        const categoriesList = document.getElementById('categories-list');
        const fieldsList = document.getElementById('fields-list');
        const feeRules = document.getElementById('fee-rules');

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        /** Read category names from inputs before re-rendering (prevents losing typed text). */
        function syncCategoriesFromDom() {
            categoriesList.querySelectorAll('.input-group').forEach((row, index) => {
                if (!categories[index]) {
                    return;
                }
                const nameInput = row.querySelector('input[name$="[name]"]');
                if (nameInput) {
                    categories[index].name = nameInput.value;
                }
            });
        }

        /** Read field editor values before re-rendering (prevents losing edits). */
        function syncFieldsFromDom() {
            fieldsList.querySelectorAll('.field-card').forEach((card) => {
                const index = parseInt(card.dataset.index, 10);
                if (Number.isNaN(index) || !fields[index]) {
                    return;
                }
                const val = (suffix) => {
                    const el = card.querySelector(`[name="fields[${index}][${suffix}]"]`);
                    return el ? el.value : '';
                };
                const checked = (suffix) => {
                    const el = card.querySelector(`[name="fields[${index}][${suffix}]"]`);
                    return el ? el.checked : false;
                };

                fields[index].field_key = val('field_key');
                fields[index].label = val('label');
                fields[index].field_type = val('field_type') || fields[index].field_type;
                fields[index].placeholder = val('placeholder');
                fields[index].options_text = val('options_text');
                fields[index].is_required = checked('is_required');
            });
        }

        function renderCategories({ refreshFields = true } = {}) {
            categoriesList.innerHTML = '';
            categories.forEach((cat, index) => {
                const row = document.createElement('div');
                row.className = 'input-group';
                row.innerHTML = `
                    <input type="hidden" name="categories[${index}][id]" value="${escapeHtml(cat.id ?? '')}">
                    <input type="text" name="categories[${index}][name]" class="form-control" value="${escapeHtml(cat.name)}" required placeholder="Category name">
                    <button type="button" class="btn btn-outline-danger remove-category" data-index="${index}">Remove</button>
                `;
                categoriesList.appendChild(row);
            });
            renderFeeRules();
            if (refreshFields) {
                syncFieldsFromDom();
                renderFields();
            }
        }

        function renderFeeRules() {
            const enabled = document.getElementById('fees_enabled').checked;
            feeRules.innerHTML = '';
            if (!enabled) return;
            categories.forEach((cat, index) => {
                const col = document.createElement('div');
                col.className = 'col-md-4';
                const amount = cat.id && initialFees[cat.id] ? initialFees[cat.id].amount : '';
                col.innerHTML = `
                    <label class="form-label small">${escapeHtml(cat.name)} fee (PKR)</label>
                    <input type="number" step="0.01" min="0" name="fee_amounts[${index}]" class="form-control" value="${escapeHtml(amount)}">
                `;
                feeRules.appendChild(col);
            });
        }

        function conditionMatrix(field, fieldIndex) {
            let html = '<div class="table-responsive mt-2"><table class="table table-sm mb-0"><thead><tr><th>Category</th><th>Visible</th><th>Required</th></tr></thead><tbody>';
            categories.forEach((cat, catIndex) => {
                const cond = (field.conditions || []).find(c => c.category_index === catIndex) || {};
                html += `<tr>
                    <td>${escapeHtml(cat.name)}</td>
                    <td><input type="checkbox" name="fields[${fieldIndex}][conditions][${catIndex}][is_visible]" value="1" ${cond.is_visible !== false ? 'checked' : ''}>
                        <input type="hidden" name="fields[${fieldIndex}][conditions][${catIndex}][category_index]" value="${catIndex}"></td>
                    <td><input type="checkbox" name="fields[${fieldIndex}][conditions][${catIndex}][is_required]" value="1" ${cond.is_required ? 'checked' : ''}></td>
                </tr>`;
            });
            html += '</tbody></table></div>';
            return html;
        }

        function renderFields() {
            fieldsList.innerHTML = '';
            fields.forEach((field, index) => {
                const card = document.createElement('div');
                card.className = 'card border field-card';
                card.dataset.index = index;
                const typeOptions = Object.entries(fieldTypes).map(([val, label]) =>
                    `<option value="${val}" ${field.field_type === val ? 'selected' : ''}>${label}</option>`
                ).join('');
                card.innerHTML = `
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small drag-handle" style="cursor:grab"><i class="bi bi-grip-vertical"></i> Drag</span>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-field">Remove</button>
                        </div>
                        <input type="hidden" name="fields[${index}][registration_field_id]" value="${escapeHtml(field.registration_field_id ?? '')}">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label small">Field key</label>
                                <input type="text" name="fields[${index}][field_key]" class="form-control form-control-sm" value="${escapeHtml(field.field_key)}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Label</label>
                                <input type="text" name="fields[${index}][label]" class="form-control form-control-sm" value="${escapeHtml(field.label)}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Type</label>
                                <select name="fields[${index}][field_type]" class="form-select form-select-sm field-type">${typeOptions}</select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">Placeholder</label>
                                <input type="text" name="fields[${index}][placeholder]" class="form-control form-control-sm" value="${escapeHtml(field.placeholder || '')}">
                            </div>
                            <div class="col-12 options-wrap ${field.field_type === 'select' && !linkedFieldKeys.includes(field.field_key) ? '' : 'd-none'}">
                                <label class="form-label small">Options (one per line)</label>
                                <textarea name="fields[${index}][options_text]" class="form-control form-control-sm" rows="2">${escapeHtml(field.options_text || '')}</textarea>
                            </div>
                            <div class="col-12 linked-options-note ${linkedFieldKeys.includes(field.field_key) ? '' : 'd-none'}">
                                <p class="small text-muted mb-0">Options are loaded from the university <strong>${escapeHtml(field.field_key)}</strong> table.</p>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fields[${index}][is_required]" value="1" id="req-${index}" ${field.is_required ? 'checked' : ''}>
                                    <label class="form-check-label small" for="req-${index}">Required by default (when no category rule)</label>
                                </div>
                            </div>
                        </div>
                        <p class="small fw-semibold mt-3 mb-1">Conditional rules</p>
                        <div class="conditional-rules-slot">${conditionMatrix(field, index)}</div>
                    </div>
                `;
                fieldsList.appendChild(card);
            });

            if (fieldsList._sortable) {
                fieldsList._sortable.destroy();
            }
            fieldsList._sortable = new Sortable(fieldsList, { handle: '.drag-handle', animation: 150 });
        }

        document.getElementById('add-category').addEventListener('click', () => {
            syncCategoriesFromDom();
            categories.push({ id: null, name: '' });
            renderCategories();
        });

        categoriesList.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-category') && categories.length > 1) {
                syncCategoriesFromDom();
                categories.splice(parseInt(e.target.dataset.index, 10), 1);
                renderCategories();
            }
        });

        document.getElementById('add-custom-field').addEventListener('click', () => {
            syncFieldsFromDom();
            fields.push({
                registration_field_id: null,
                field_key: 'custom_' + (fields.length + 1),
                label: 'Custom field',
                field_type: 'text',
                placeholder: '',
                is_required: false,
                options_text: '',
                conditions: [],
            });
            renderFields();
        });

        fieldsList.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-field')) {
                syncFieldsFromDom();
                const card = e.target.closest('.field-card');
                fields.splice(parseInt(card.dataset.index, 10), 1);
                renderFields();
            }
        });

        fieldsList.addEventListener('change', (e) => {
            if (e.target.classList.contains('field-type')) {
                const card = e.target.closest('.field-card');
                const wrap = card.querySelector('.options-wrap');
                wrap.classList.toggle('d-none', e.target.value !== 'select');
            }
        });

        document.querySelectorAll('.library-pick').forEach(btn => {
            btn.addEventListener('click', () => {
                syncFieldsFromDom();
                const options = (btn.dataset.options || '')
                    .split('|')
                    .map((line) => line.trim())
                    .filter(Boolean);
                fields.push({
                    registration_field_id: btn.dataset.id,
                    field_key: btn.dataset.fieldKey,
                    label: btn.dataset.label,
                    field_type: btn.dataset.fieldType,
                    placeholder: '',
                    is_required: false,
                    options_text: options.join('\n'),
                    conditions: [],
                });
                renderFields();
                bootstrap.Modal.getInstance(document.getElementById('libraryModal')).hide();
            });
        });

        document.getElementById('fees_enabled').addEventListener('change', renderFeeRules);

        document.getElementById('form-builder').addEventListener('submit', () => {
            syncCategoriesFromDom();
            syncFieldsFromDom();
        });

        renderCategories();
        if (!fields.length) {
            document.getElementById('add-custom-field').click();
        } else {
            renderFields();
        }
    </script>
@endpush
