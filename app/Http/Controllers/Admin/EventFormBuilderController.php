<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventFeeRule;
use App\Models\EventFieldCondition;
use App\Models\EventFormField;
use App\Models\EventParticipantCategory;
use App\Models\RegistrationField;
use App\Services\EventFormFieldResolver;
use App\Services\RegistrationFieldOptionsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EventFormBuilderController extends Controller
{
    public function edit(Event $event): View
    {
        $this->authorize('manageForm', $event);

        $event->load([
            'participantCategories',
            'formFields.conditions',
            'feeRules',
        ]);

        $libraryFields = RegistrationField::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.events.form-builder', compact('event', 'libraryFields'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('manageForm', $event);

        $validated = $request->validate([
            'fees_enabled' => ['nullable', 'boolean'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*.name' => ['required', 'string', 'max:100'],
            'categories.*.id' => ['nullable', 'integer'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.field_key' => ['required', 'string', 'max:100', 'alpha_dash'],
            'fields.*.field_type' => ['required', 'string'],
            'fields.*.placeholder' => ['nullable', 'string', 'max:255'],
            'fields.*.is_required' => ['nullable', 'boolean'],
            'fields.*.registration_field_id' => ['nullable', 'integer', 'exists:registration_fields,id'],
            'fields.*.options_text' => ['nullable', 'string'],
            'fields.*.conditions' => ['nullable', 'array'],
            'fields.*.conditions.*.category_index' => ['required', 'integer'],
            'fields.*.conditions.*.is_visible' => ['nullable', 'boolean'],
            'fields.*.conditions.*.is_required' => ['nullable', 'boolean'],
            'fee_amounts' => ['nullable', 'array'],
            'fee_amounts.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($event, $validated, $request): void {
            $event->update(['fees_enabled' => $request->boolean('fees_enabled')]);

            $categoryMap = [];
            foreach ($validated['categories'] as $index => $categoryData) {
                $slug = Str::slug($categoryData['name']);
                if (! empty($categoryData['id'])) {
                    $category = EventParticipantCategory::query()
                        ->where('event_id', $event->id)
                        ->where('id', $categoryData['id'])
                        ->firstOrFail();
                    $category->update([
                        'name' => $categoryData['name'],
                        'slug' => $slug,
                        'sort_order' => $index,
                    ]);
                } else {
                    $category = EventParticipantCategory::create([
                        'event_id' => $event->id,
                        'name' => $categoryData['name'],
                        'slug' => $slug,
                        'sort_order' => $index,
                    ]);
                }
                $categoryMap[$index] = $category;
            }

            $submittedCategoryIds = collect($categoryMap)->pluck('id')->all();
            EventParticipantCategory::query()
                ->where('event_id', $event->id)
                ->whereNotIn('id', $submittedCategoryIds)
                ->get()
                ->each(function (EventParticipantCategory $category): void {
                    if ($category->registrations()->count() === 0) {
                        $category->delete();
                    }
                });

            $keptFieldIds = [];
            foreach ($validated['fields'] as $index => $fieldData) {
                $options = null;
                if (in_array($fieldData['field_key'], RegistrationFieldOptionsService::linkedFieldKeys(), true)) {
                    $fieldData['field_type'] = RegistrationField::TYPE_SELECT;
                    $options = null;
                } elseif (($fieldData['field_type'] ?? '') === RegistrationField::TYPE_SELECT) {
                    $options = collect(preg_split('/\r\n|\r|\n/', $fieldData['options_text'] ?? ''))
                        ->map(fn ($line) => trim($line))
                        ->filter()
                        ->values()
                        ->all();
                }

                $field = EventFormField::updateOrCreate(
                    [
                        'event_id' => $event->id,
                        'field_key' => $fieldData['field_key'],
                    ],
                    [
                        'registration_field_id' => $fieldData['registration_field_id'] ?? null,
                        'label' => $fieldData['label'],
                        'placeholder' => $fieldData['placeholder'] ?? null,
                        'field_type' => $fieldData['field_type'],
                        'options' => $options,
                        'is_required' => ! empty($fieldData['is_required']),
                        'sort_order' => $index,
                    ]
                );

                $keptFieldIds[] = $field->id;

                EventFieldCondition::query()->where('event_form_field_id', $field->id)->delete();

                foreach ($fieldData['conditions'] ?? [] as $condition) {
                    $categoryIndex = (int) $condition['category_index'];
                    if (! isset($categoryMap[$categoryIndex])) {
                        continue;
                    }

                    EventFieldCondition::create([
                        'event_form_field_id' => $field->id,
                        'event_participant_category_id' => $categoryMap[$categoryIndex]->id,
                        'is_visible' => ! empty($condition['is_visible']),
                        'is_required' => ! empty($condition['is_required']),
                    ]);
                }
            }

            EventFormField::query()
                ->where('event_id', $event->id)
                ->whereNotIn('id', $keptFieldIds)
                ->delete();

            EventFeeRule::query()->where('event_id', $event->id)->delete();
            if ($request->boolean('fees_enabled')) {
                foreach ($validated['fee_amounts'] ?? [] as $categoryIndex => $amount) {
                    if (! isset($categoryMap[$categoryIndex])) {
                        continue;
                    }

                    EventFeeRule::create([
                        'event_id' => $event->id,
                        'event_participant_category_id' => $categoryMap[$categoryIndex]->id,
                        'amount' => $amount ?? 0,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.events.form-builder.edit', $event)
            ->with('success', 'Registration form configuration saved.');
    }

    public function preview(Request $request, Event $event, EventFormFieldResolver $resolver): View
    {
        $this->authorize('manageForm', $event);

        $event->load(['participantCategories', 'formFields.conditions', 'feeRules']);

        $categoryId = $request->integer('category_id');
        $category = $event->participantCategories->firstWhere('id', $categoryId)
            ?? $event->participantCategories->first();

        $resolvedFields = $resolver->fieldsForCategory($event, $category);

        return view('admin.events.form-preview', compact('event', 'category', 'resolvedFields'));
    }
}
