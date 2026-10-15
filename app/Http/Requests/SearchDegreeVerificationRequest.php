<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchDegreeVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $maxYear = now()->year - 2;

        return [
            'year' => ['required', 'integer', 'min:1996', 'max:'.$maxYear],
            'level' => ['required', 'integer', Rule::in([0, 1, 2, 3, 4])],
            'criteria' => ['required', 'string', Rule::in(['reg_no', 'degree_id'])],
            'term' => ['required', 'string', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('term') && is_string($this->term)) {
            $this->merge([
                'term' => trim($this->term),
            ]);
        }
    }
}
