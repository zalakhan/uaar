<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\Department;
use App\Models\Faculty;
use App\Models\User;

trait ProvidesDepartmentOptions
{
    /**
     * Active faculties for select dropdowns.
     */
    protected function facultiesForSelect()
    {
        return Faculty::where('is_active', true)->orderBy('name')->pluck('name', 'id');
    }

    /**
     * Departments grouped by faculty_id for dependent dropdown JavaScript.
     */
    protected function departmentsByFacultyForJs(?User $user = null): array
    {
        $query = Department::where('is_active', true)->orderBy('name');

        if ($user && ! $user->isSuperAdmin()) {
            $query->where('id', $user->department_id);
        }

        return $query->get(['id', 'name', 'faculty_id'])
            ->groupBy('faculty_id')
            ->map(fn ($group) => $group->map(fn ($department) => [
                'id' => $department->id,
                'name' => $department->name,
            ])->values())
            ->toArray();
    }
}
