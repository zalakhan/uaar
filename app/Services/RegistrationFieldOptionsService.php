<?php

namespace App\Services;

use App\Models\Department;
use App\Models\EventFormField;
use App\Models\Faculty;
use App\Models\RegistrationField;

class RegistrationFieldOptionsService
{
    public const LINKED_FACULTY = 'faculty';

    public const LINKED_DEPARTMENT = 'department';

    /**
     * Field keys that load select options from the database.
     *
     * @return list<string>
     */
    public static function linkedFieldKeys(): array
    {
        return [self::LINKED_FACULTY, self::LINKED_DEPARTMENT];
    }

    public function isLinkedField(EventFormField|RegistrationField|string $field): bool
    {
        $key = is_string($field) ? $field : $field->field_key;

        return in_array($key, self::linkedFieldKeys(), true);
    }

    /**
     * Select options as id => label (stored value is the id).
     *
     * @return array<int|string, string>
     */
    public function selectOptions(EventFormField|RegistrationField $field): array
    {
        if ($this->isLinkedField($field)) {
            return match ($field->field_key) {
                self::LINKED_FACULTY => Faculty::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all(),
                self::LINKED_DEPARTMENT => Department::query()
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->all(),
                default => [],
            };
        }

        return collect($field->options ?? [])
            ->mapWithKeys(fn (string $option) => [$option => $option])
            ->all();
    }

    /**
     * Format options for JSON / frontend consumers.
     *
     * @return list<array{value: string, label: string}>
     */
    public function selectOptionsForApi(EventFormField $field): array
    {
        return collect($this->selectOptions($field))
            ->map(fn (string $label, $value) => [
                'value' => (string) $value,
                'label' => $label,
            ])
            ->values()
            ->all();
    }

    /**
     * Human-readable label for a stored answer (ids for linked fields).
     */
    public function displayValue(string $fieldKey, ?string $stored): string
    {
        if ($stored === null || $stored === '') {
            return '';
        }

        return match ($fieldKey) {
            self::LINKED_FACULTY => Faculty::query()->find($stored)?->name ?? $stored,
            self::LINKED_DEPARTMENT => Department::query()->find($stored)?->name ?? $stored,
            default => $stored,
        };
    }
}
