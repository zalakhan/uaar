<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait ChecksDepartmentAccess
{
    /**
     * Super admins see all departments; staff can view all; dept_admin only their own.
     */
    protected function belongsToUserDepartment(User $user, ?int $departmentId): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->department_id && $user->department_id === $departmentId;
    }

    /**
     * View access: staff may view records from any department.
     */
    protected function canViewDepartmentRecord(User $user, ?int $departmentId): bool
    {
        if ($user->isSuperAdmin() || $user->hasRole('staff')) {
            return true;
        }

        return $this->belongsToUserDepartment($user, $departmentId);
    }
}
