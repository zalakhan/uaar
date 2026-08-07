<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesNewsDepartmentScope;
use App\Rules\NotEmptyHtml;
use Illuminate\Foundation\Http\FormRequest;

class StoreNewsRequest extends FormRequest
{
    use ValidatesNewsDepartmentScope;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize checkbox input before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->boolean('status'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', new NotEmptyHtml],
            'published_date' => ['required', 'date'],
            'status' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'max:2048'],
        ], $this->departmentScopeRules());
    }

    /**
     * Custom attribute names for validation messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'description' => 'content',
            'published_date' => 'published date',
            'department_scope' => 'department',
        ];
    }

    /**
     * Return validated data with sanitized HTML content.
     */
    public function validated($key = null, $default = null): mixed
    {
        $data = parent::validated($key, $default);

        if ($key !== null) {
            return $data;
        }

        $data['description'] = clean($data['description'], 'news');
        $data['status'] = (bool) $data['status'];

        return $this->applyDepartmentScope($data);
    }
}
