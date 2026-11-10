@extends('layouts.app')

@section('page-title', $registration->registration_number)

@section('content')
    <a href="{{ route('admin.events.registrations.index', $event) }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to list</a>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Registration number</dt>
                <dd class="col-sm-9">{{ $registration->registration_number }}</dd>
                <dt class="col-sm-3">Category</dt>
                <dd class="col-sm-9">{{ $registration->category_label }}</dd>
                <dt class="col-sm-3">Submitted</dt>
                <dd class="col-sm-9">{{ $registration->submitted_at?->format('d M Y H:i') }}</dd>
                @if ($event->fees_enabled)
                    <dt class="col-sm-3">Fee (PKR)</dt>
                    <dd class="col-sm-9">{{ $registration->fee_amount !== null ? number_format((float) $registration->fee_amount, 2) : '—' }}</dd>
                @endif
            </dl>
            <hr>
            <h2 class="h6">Submitted answers</h2>
            <dl class="row mb-0">
                @foreach ($registration->answers as $answer)
                    <dt class="col-sm-4">{{ $answer->label }}</dt>
                    <dd class="col-sm-8">
                        @if ($answer->field_type === 'file' && $answer->file_path)
                            <a href="{{ asset('storage/'.$answer->file_path) }}" target="_blank">Download file</a>
                        @else
                            {{ app(\App\Services\RegistrationFieldOptionsService::class)->displayValue($answer->field_key, $answer->value) ?: '—' }}
                        @endif
                    </dd>
                @endforeach
            </dl>
        </div>
    </div>
@endsection
