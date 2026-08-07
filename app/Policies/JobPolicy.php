<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use App\Policies\Concerns\ChecksDepartmentAccess;

class JobPolicy
{
    use ChecksDepartmentAccess;

    public function viewAny(User $user): bool
    {
        return $user->can('jobs.view');
    }

    public function view(User $user, Job $job): bool
    {
        return $user->can('jobs.view')
            && $this->canViewDepartmentRecord($user, $job->department_id);
    }

    public function create(User $user): bool
    {
        return $user->can('jobs.create');
    }

    public function update(User $user, Job $job): bool
    {
        return $user->can('jobs.edit')
            && $this->belongsToUserDepartment($user, $job->department_id);
    }

    public function delete(User $user, Job $job): bool
    {
        return $user->can('jobs.delete')
            && $this->belongsToUserDepartment($user, $job->department_id);
    }
}
