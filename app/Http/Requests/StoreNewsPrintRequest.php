<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNewsPrintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clippings' => ['required', 'array', 'min:1'],
            'clippings.*.news_file' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'clippings.*.newspaper_id' => ['required', 'exists:newspapers,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'clippings.*.news_file' => 'clipping image',
            'clippings.*.newspaper_id' => 'newspaper',
        ];
    }
}
