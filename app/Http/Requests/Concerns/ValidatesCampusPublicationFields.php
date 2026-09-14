<?php

namespace App\Http\Requests\Concerns;

use App\Models\CampusPublication;
use Illuminate\Validation\Rule;

trait ValidatesCampusPublicationFields
{
    /**
     * Shared validation rules for campus publication forms.
     *
     * @return array<string, mixed>
     */
    protected function campusPublicationRules(bool $requireFile = true): array
    {
        $fileRules = $requireFile ? ['required'] : ['nullable'];

        return [
            'type' => ['required', Rule::in(CampusPublication::types())],
            'duration' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:2014', 'max:'.(now()->year + 1)],
            'file' => array_merge($fileRules, ['file', 'mimes:pdf', 'max:4096']),
        ];
    }

    /**
     * Strip unsafe HTML from plain-text fields before validation.
     */
    protected function prepareCampusPublicationForValidation(): void
    {
        if ($this->has('duration')) {
            $this->merge([
                'duration' => strip_tags((string) $this->input('duration')),
            ]);
        }

        if ($this->has('year')) {
            $this->merge([
                'year' => (int) $this->input('year'),
            ]);
        }
    }
}
