<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventFormField;
use App\Models\EventParticipantCategory;
use Illuminate\Support\Collection;

class EventFormFieldResolver
{
    public function __construct(
        private RegistrationFieldOptionsService $linkedFieldOptions,
    ) {}

    /**
     * Resolve visible fields and required flags for a participant category.
     *
     * @return Collection<int, array{field: EventFormField, is_required: bool}>
     */
    public function fieldsForCategory(Event $event, ?EventParticipantCategory $category): Collection
    {
        $event->loadMissing(['formFields.conditions']);

        return $event->formFields->map(function (EventFormField $field) use ($category) {
            $condition = null;
            if ($category) {
                $condition = $field->conditions->firstWhere(
                    'event_participant_category_id',
                    $category->id
                );
            }

            $visible = $condition ? $condition->is_visible : true;
            if (! $visible) {
                return null;
            }

            $required = $condition
                ? $condition->is_required
                : (bool) $field->is_required;

            return [
                'field' => $field,
                'is_required' => $required,
            ];
        })->filter()->values();
    }

    /**
     * Build Laravel validation rules for dynamic form submission.
     *
     * @return array<string, mixed>
     */
    public function validationRules(Event $event, EventParticipantCategory $category): array
    {
        $rules = [
            'participant_category_id' => ['required', 'integer', 'exists:event_participant_categories,id'],
            'website' => ['nullable', 'max:0'], // honeypot
        ];

        foreach ($this->fieldsForCategory($event, $category) as $item) {
            /** @var EventFormField $field */
            $field = $item['field'];
            $key = 'fields.'.$field->field_key;
            $fieldRules = $this->rulesForField($field, $item['is_required']);
            $rules[$key] = $fieldRules;
        }

        return $rules;
    }

    /**
     * @return list<string>
     */
    private function rulesForField(EventFormField $field, bool $required): array
    {
        $rules = $required ? ['required'] : ['nullable'];

        switch ($field->field_type) {
            case 'email':
                $rules[] = 'email';
                $rules[] = 'max:255';
                break;
            case 'tel':
                $rules[] = 'string';
                $rules[] = 'max:20';
                break;
            case 'date':
                $rules[] = 'date';
                break;
            case 'textarea':
                $rules[] = 'string';
                $rules[] = 'max:5000';
                break;
            case 'file':
                $rules[] = 'file';
                $rules[] = 'max:5120';
                $rules[] = 'mimes:pdf,jpg,jpeg,png,doc,docx';
                break;
            case 'select':
                if ($this->linkedFieldOptions->isLinkedField($field)) {
                    $rules[] = 'integer';
                    if ($field->field_key === RegistrationFieldOptionsService::LINKED_DEPARTMENT) {
                        $rules[] = 'exists:departments,id';
                    } elseif ($field->field_key === RegistrationFieldOptionsService::LINKED_FACULTY) {
                        $rules[] = 'exists:faculties,id';
                    }
                } else {
                    $rules[] = 'string';
                    $rules[] = 'max:255';
                    if (! empty($field->options)) {
                        $rules[] = 'in:'.implode(',', $field->options);
                    }
                }
                break;
            default:
                $rules[] = 'string';
                $rules[] = 'max:255';
                break;
        }

        $custom = $field->validation_rules ?? [];
        foreach ($custom as $rule) {
            if (is_string($rule)) {
                $rules[] = $rule;
            }
        }

        return array_values(array_unique($rules));
    }
}
