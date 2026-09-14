<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesCampusPublicationFields;
use Illuminate\Foundation\Http\FormRequest;

class StoreCampusPublicationRequest extends FormRequest
{
    use ValidatesCampusPublicationFields;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize input before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->prepareCampusPublicationForValidation();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->campusPublicationRules(requireFile: true);
    }

    /**
     * Custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => 'publication type',
            'duration' => 'duration',
            'year' => 'year',
            'file' => 'PDF file',
        ];
    }
}
