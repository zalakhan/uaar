<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFeeRule;
use App\Models\EventParticipantCategory;
use App\Models\EventRegistration;
use App\Models\EventRegistrationAnswer;
use App\Mail\EventRegistrationConfirmationMail;
use App\Models\RegistrationField;
use App\Services\EventDuplicateRegistrationChecker;
use App\Services\EventFormFieldResolver;
use App\Services\RegistrationFieldOptionsService;
use App\Services\RegistrationNumberGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EventRegistrationPublicController extends Controller
{
    public function show(
        string $slug,
        EventFormFieldResolver $resolver,
        RegistrationFieldOptionsService $linkedFieldOptions,
    ): View {
        $event = Event::query()
            ->where('public_slug', $slug)
            ->with(['participantCategories', 'formFields.conditions', 'feeRules'])
            ->firstOrFail();

        $selectedCategory = old('participant_category_id')
            ? $event->participantCategories->firstWhere('id', (int) old('participant_category_id'))
            : null;

        $resolvedFields = $selectedCategory
            ? $resolver->fieldsForCategory($event, $selectedCategory)
            : collect();

        return view('public.event-registration', [
            'event' => $event,
            'resolvedFields' => $resolvedFields,
            'selectedCategory' => $selectedCategory,
            'feeRules' => $event->feeRules->keyBy('event_participant_category_id'),
            'linkedFieldOptions' => $linkedFieldOptions,
        ]);
    }

    public function store(
        Request $request,
        string $slug,
        EventFormFieldResolver $resolver,
        RegistrationNumberGenerator $numberGenerator,
        EventDuplicateRegistrationChecker $duplicateChecker,
    ): RedirectResponse|View {
        $event = Event::query()
            ->where('public_slug', $slug)
            ->with(['participantCategories', 'formFields.conditions', 'feeRules'])
            ->firstOrFail();

        if (! $event->isOpenForRegistration()) {
            return redirect()
                ->route('event-registration.show', $slug)
                ->with('error', 'Registration is closed for this event.');
        }

        // Honeypot — bots often fill hidden fields
        if ($request->filled('website')) {
            return redirect()
                ->route('event-registration.show', $slug)
                ->with('success', 'Registration submitted successfully.');
        }

        $category = EventParticipantCategory::query()
            ->where('event_id', $event->id)
            ->where('id', $request->input('participant_category_id'))
            ->firstOrFail();

        $rules = $resolver->validationRules($event, $category);
        $resolvedForMessages = $resolver->fieldsForCategory($event, $category);

        $messages = [
            'participant_category_id.required' => 'This field is required.',
        ];
        $attributes = [
            'participant_category_id' => 'Participant category',
        ];

        foreach ($resolvedForMessages as $item) {
            $fieldKey = $item['field']->field_key;
            $attributes['fields.'.$fieldKey] = $item['field']->label;
            $messages['fields.'.$fieldKey.'.required'] = 'This field is required.';
        }

        $validated = $request->validate($rules, $messages, $attributes);

        $fieldsInput = $validated['fields'] ?? [];

        $existingRegistration = $duplicateChecker->findExisting($event, $fieldsInput);
        if ($existingRegistration) {
            return redirect()
                ->route('event-registration.success', [
                    'slug' => $slug,
                    'registration' => $existingRegistration->registration_number,
                ])
                ->with('already_registered', true);
        }

        $feeAmount = null;
        if ($event->fees_enabled) {
            $feeRule = EventFeeRule::query()
                ->where('event_id', $event->id)
                ->where('event_participant_category_id', $category->id)
                ->first();

            $feeAmount = $feeRule?->amount;
        }

        $resolved = $resolver->fieldsForCategory($event, $category);
        $visibleKeys = $resolved->pluck('field.field_key')->all();

        $registration = DB::transaction(function () use (
            $event,
            $category,
            $fieldsInput,
            $visibleKeys,
            $resolved,
            $numberGenerator,
            $feeAmount,
            $request,
            $duplicateChecker,
        ) {
            // Re-check inside the transaction to block double-submit race conditions.
            $existing = $duplicateChecker->findExisting($event, $fieldsInput);
            if ($existing) {
                return $existing;
            }

            $registration = EventRegistration::create([
                'event_id' => $event->id,
                'event_participant_category_id' => $category->id,
                'registration_number' => $numberGenerator->generate($event),
                'category_label' => $category->name,
                'fee_amount' => $feeAmount,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 500),
                'submitted_at' => now(),
            ]);

            foreach ($resolved as $item) {
                $field = $item['field'];
                if (! in_array($field->field_key, $visibleKeys, true)) {
                    continue;
                }

                $value = $fieldsInput[$field->field_key] ?? null;
                $filePath = null;

                if ($field->field_type === RegistrationField::TYPE_FILE && $request->hasFile('fields.'.$field->field_key)) {
                    $filePath = $request->file('fields.'.$field->field_key)
                        ->store('event-registrations/'.$event->id, 'public');
                    $value = basename($filePath);
                }

                EventRegistrationAnswer::create([
                    'event_registration_id' => $registration->id,
                    'event_form_field_id' => $field->id,
                    'field_key' => $field->field_key,
                    'label' => $field->label,
                    'field_type' => $field->field_type,
                    'value' => is_string($value) ? $value : (string) $value,
                    'file_path' => $filePath,
                ]);
            }

            return $registration;
        });

        $isNewRegistration = (bool) $registration->wasRecentlyCreated;

        if ($isNewRegistration) {
            $this->sendConfirmationEmail($event, $registration, $fieldsInput);
        }

        return redirect()
            ->route('event-registration.success', [
                'slug' => $slug,
                'registration' => $registration->registration_number,
            ])
            ->with('already_registered', ! $isNewRegistration);
    }

    /**
     * Notify participant by email when an email address was submitted on the form.
     */
    private function sendConfirmationEmail(Event $event, EventRegistration $registration, array $fieldsInput): void
    {
        $email = $fieldsInput['email'] ?? null;
        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($email)->send(new EventRegistrationConfirmationMail($event, $registration));
        } catch (\Throwable $exception) {
            Log::warning('Event registration confirmation email failed', [
                'registration_id' => $registration->id,
                'email' => $email,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function success(string $slug, string $registration): View
    {
        $event = Event::query()->where('public_slug', $slug)->firstOrFail();

        $record = EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('registration_number', $registration)
            ->firstOrFail();

        return view('public.event-registration-success', compact('event', 'record'));
    }

    /**
     * JSON field map for live category switching on the public form.
     */
    public function fields(
        Request $request,
        string $slug,
        EventFormFieldResolver $resolver,
        RegistrationFieldOptionsService $linkedFieldOptions,
    ): \Illuminate\Http\JsonResponse
    {
        $event = Event::query()
            ->where('public_slug', $slug)
            ->with(['participantCategories', 'formFields.conditions', 'feeRules'])
            ->firstOrFail();

        $category = EventParticipantCategory::query()
            ->where('event_id', $event->id)
            ->where('id', $request->integer('category_id'))
            ->firstOrFail();

        $resolved = $resolver->fieldsForCategory($event, $category);
        $fee = null;
        if ($event->fees_enabled) {
            $fee = $event->feeRules->firstWhere('event_participant_category_id', $category->id)?->amount;
        }

        return response()->json([
            'fields' => $resolved->map(fn ($item) => [
                'key' => $item['field']->field_key,
                'label' => $item['field']->label,
                'type' => $item['field']->field_type,
                'placeholder' => $item['field']->placeholder,
                'options' => $linkedFieldOptions->selectOptionsForApi($item['field']),
                'required' => $item['is_required'],
            ])->values(),
            'fee' => $fee,
        ]);
    }
}
