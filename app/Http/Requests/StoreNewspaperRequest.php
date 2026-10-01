<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNewspaperRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'newspaper_name' => ['required', 'string', 'max:255', Rule::unique('newspapers', 'newspaper_name')],
        ];
    }
}
