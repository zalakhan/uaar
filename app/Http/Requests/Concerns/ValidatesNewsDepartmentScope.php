<?php

namespace App\Http\Requests\Concerns;

use App\Models\Department;
use Illuminate\Validation\Rule;

trait ValidatesNewsDepartmentScope
{
    /**
     * Form value used for university-wide (non-department) news.
     */
    public const MAIN_WEBSITE_SCOPE = 'main';

    /**
     * Validation rules for the department scope field.
     *
     * @return array<string, mixed>
     */
    protected function departmentScopeRules(): array
    {
        return [
            'department_scope' => [
                'required',
                'string',
                Rule::in(array_merge(
                    [self::MAIN_WEBSITE_SCOPE],
                    Department::orderBy('name')->pluck('id')->map(fn ($id) => (string) $id)->all()
                )),
            ],
        ];
    }

    /**
     * Convert department_scope to nullable department_id for storage.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function applyDepartmentScope(array $data): array
    {
        $data['department_id'] = $data['department_scope'] === self::MAIN_WEBSITE_SCOPE
            ? null
            : (int) $data['department_scope'];

        unset($data['department_scope']);

        return $data;
    }
}
