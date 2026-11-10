@extends('layouts.public')

@section('title', $event->title)

@section('content')
    <div class="public-card">
        <aside class="public-card__aside" aria-label="Registration steps">
            <h2 class="public-card__aside-title">Event registration</h2>
            <p class="public-card__aside-text">
                Complete the form below to register for this event. Fields may change based on your participant category.
            </p>
            <ul class="public-card__aside-steps">
                <li><span class="public-card__step-dot is-active" aria-hidden="true"></span> Choose category</li>
                <li><span class="public-card__step-dot" aria-hidden="true"></span> Fill in your details</li>
                <li><span class="public-card__step-dot" aria-hidden="true"></span> Receive your registration number</li>
            </ul>
            <div class="public-card__aside-meta">
                <span>{{ $event->event_number }}</span>
                @if ($event->event_date)
                    <span>Date: {{ $event->event_date->format('d M Y') }}</span>
                @endif
                @if ($event->venue)
                    <span>Venue: {{ $event->venue }}</span>
                @endif
                @if ($event->registration_deadline)
                    <span>Deadline: {{ $event->registration_deadline->format('d M Y') }}</span>
                @endif
            </div>
        </aside>

        <div class="public-card__body">
            <p class="public-card__event-ref">{{ $event->event_number }}</p>
            <h2 class="public-card__heading">{{ $event->title }}</h2>
            @if ($event->description)
                <p class="public-card__subtitle">{!! nl2br(e(\Illuminate\Support\Str::limit(strip_tags($event->description), 200))) !!}</p>
            @endif

            @if (session('error'))
                <x-public.alert type="danger">{{ session('error') }}</x-public.alert>
            @endif

            @if (! $event->isOpenForRegistration())
                <x-public.alert type="warning">Registration is currently closed for this event.</x-public.alert>
            @else
                <form method="POST" action="{{ route('event-registration.store', $event->public_slug) }}" enctype="multipart/form-data" id="registration-form" novalidate>
                    @csrf
                    <div class="public-sr-only" aria-hidden="true">
                        <label for="website">Website</label>
                        <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                    </div>

                    <x-public.select
                        name="participant_category_id"
                        id="participant_category_id"
                        label="Participant category"
                        placeholder="Select category"
                        :options="$event->participantCategories->pluck('name', 'id')->all()"
                        :value="old('participant_category_id')"
                        required
                    />

                    @if ($event->fees_enabled)
                        @php
                            $initialFee = $selectedCategory
                                ? $feeRules->get($selectedCategory->id)?->amount
                                : null;
                        @endphp
                        <x-public.alert type="info" id="fee-display">
                            Registration fee: <strong id="fee-amount">{{ $initialFee !== null ? number_format((float) $initialFee, 2) : '0.00' }}</strong> PKR
                        </x-public.alert>
                    @endif

                    <div id="dynamic-fields">
                        @if ($resolvedFields->isNotEmpty())
                            @include('partials.event-registration-fields', [
                                'resolvedFields' => $resolvedFields,
                                'publicStyle' => true,
                                'linkedFieldOptions' => $linkedFieldOptions,
                            ])
                        @else
                            <p class="small text-muted public-category-hint" id="category-fields-hint">Select a participant category to show registration fields.</p>
                        @endif
                    </div>

                    <div class="public-form-actions">
                        <x-public.button type="submit">Submit registration</x-public.button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const categorySelect = document.getElementById('participant_category_id');
        const fieldsUrl = @json(route('event-registration.fields', $event->public_slug));
        const feesEnabled = @json($event->fees_enabled);

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function renderField(field) {
            const required = field.required ? 'required' : '';
            const requiredMark = field.required ? '<span class="public-required" aria-hidden="true">*</span>' : '';
            const name = `fields[${field.key}]`;
            const id = `field-${field.key}`;
            let input = '';
            const emptyMsg = 'This field is required.';

            if (field.type === 'textarea') {
                input = `<textarea name="${name}" id="${id}" class="public-input" rows="3" placeholder="${escapeHtml(field.placeholder || '')}" ${required}></textarea>`;
            } else if (field.type === 'select') {
                const options = (field.options || []).map(o => {
                    const value = o.value !== undefined ? o.value : o;
                    const optionLabel = o.label !== undefined ? o.label : o;
                    return `<option value="${escapeHtml(value)}">${escapeHtml(optionLabel)}</option>`;
                }).join('');
                input = `<select name="${name}" id="${id}" class="public-select" ${required}><option value="">Select...</option>${options}</select>`;
            } else if (field.type === 'file') {
                input = `<input type="file" name="${name}" id="${id}" class="public-input public-input--file" ${required}>`;
            } else {
                const type = ['email', 'tel', 'date'].includes(field.type) ? field.type : 'text';
                input = `<input type="${type}" name="${name}" id="${id}" class="public-input" placeholder="${escapeHtml(field.placeholder || '')}" ${required}>`;
            }

            return `<div class="public-field dynamic-field" data-field-key="${escapeHtml(field.key)}">
                <label class="public-label" for="${id}">${escapeHtml(field.label)}${requiredMark}</label>
                ${input}
                <p class="public-field-error public-field-error--client" data-client-error role="alert">${emptyMsg}</p>
            </div>`;
        }

        function isFieldEmpty(control) {
            if (control.type === 'file') {
                return !control.files || control.files.length === 0;
            }
            if (control.tagName === 'SELECT') {
                return control.value === '';
            }
            return !String(control.value || '').trim();
        }

        function setFieldInvalid(control, isInvalid) {
            const field = control.closest('.public-field');
            if (!field) return;
            const clientError = field.querySelector('[data-client-error]');
            const serverError = field.querySelector('.public-field-error:not(.public-field-error--client)');
            control.classList.toggle('is-invalid', isInvalid);
            if (clientError && !serverError) {
                clientError.classList.toggle('is-visible', isInvalid);
            }
        }

        function validateRegistrationForm(form) {
            let valid = true;
            let firstInvalid = null;

            form.querySelectorAll('[required]').forEach((control) => {
                const empty = isFieldEmpty(control);
                setFieldInvalid(control, empty);
                if (empty) {
                    valid = false;
                    if (!firstInvalid) firstInvalid = control;
                }
            });

            if (!valid && firstInvalid) {
                firstInvalid.focus();
            }

            return valid;
        }

        async function loadCategoryFields() {
            const categoryId = categorySelect.value;
            const container = document.getElementById('dynamic-fields');

            if (!categoryId) {
                container.innerHTML =
                    '<p class="small text-muted public-category-hint" id="category-fields-hint">Select a participant category to show registration fields.</p>';
                if (feesEnabled && document.getElementById('fee-amount')) {
                    document.getElementById('fee-amount').textContent = '—';
                }
                return;
            }

            const res = await fetch(`${fieldsUrl}?category_id=${categoryId}`);
            const data = await res.json();
            container.innerHTML =
                '<div class="public-form-grid public-form-grid--dynamic">' +
                data.fields.map(renderField).join('') +
                '</div>';
            if (feesEnabled) {
                document.getElementById('fee-amount').textContent =
                    data.fee != null ? Number(data.fee).toFixed(2) : '0.00';
            }
        }

        categorySelect.addEventListener('change', loadCategoryFields);

        if (!categorySelect.value) {
            document.getElementById('dynamic-fields').innerHTML =
                '<p class="small text-muted public-category-hint" id="category-fields-hint">Select a participant category to show registration fields.</p>';
        }

        const registrationForm = document.getElementById('registration-form');
        if (registrationForm) {
            registrationForm.addEventListener('submit', (event) => {
                if (!validateRegistrationForm(registrationForm)) {
                    event.preventDefault();
                }
            });

            registrationForm.addEventListener('input', (event) => {
                const control = event.target;
                if (!control || !control.closest('.public-field')) {
                    return;
                }
                if (control.hasAttribute('required') && !isFieldEmpty(control)) {
                    setFieldInvalid(control, false);
                }
            });

            registrationForm.addEventListener('change', (event) => {
                const control = event.target;
                if (!control || !control.closest('.public-field')) {
                    return;
                }
                if (control.hasAttribute('required') && !isFieldEmpty(control)) {
                    setFieldInvalid(control, false);
                }
            });
        }
    </script>
@endpush
