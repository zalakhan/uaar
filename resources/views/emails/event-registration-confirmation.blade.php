<x-mail::message>
# Registration confirmed

Thank you for registering for **{{ $event->title }}**.

**Registration number:** {{ $registration->registration_number }}

**Participant category:** {{ $registration->category_label }}

@if ($event->event_date)
**Event date:** {{ $event->event_date->format('l, d F Y') }}
@endif

@if ($event->venue)
**Venue:** {{ $event->venue }}
@endif

@if ($event->registration_deadline)
**Registration deadline:** {{ $event->registration_deadline->format('d M Y') }}
@endif

Please keep this email and your registration number for your records.

Thanks,<br>
{{ config('mail.event_registration.from.name') }}
</x-mail::message>
