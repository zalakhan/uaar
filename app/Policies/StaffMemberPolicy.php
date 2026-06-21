<?php

namespace App\Policies;

use App\Models\StaffMember;
use App\Models\User;
use App\Policies\Concerns\ChecksDepartmentAccess;

class StaffMemberPolicy
{
    use ChecksDepartmentAccess;

    public function viewAny(User $user): bool
    {
        return $user->can('staff_members.view');
    }

    public function view(User $user, StaffMember $staffMember): bool
    {
        return $user->can('staff_members.view')
            && $this->canViewDepartmentRecord($user, $staffMember->department_id);
    }

    public function create(User $user): bool
    {
        return $user->can('staff_members.create');
    }

    public function update(User $user, StaffMember $staffMember): bool
    {
        return $user->can('staff_members.edit')
            && $this->belongsToUserDepartment($user, $staffMember->department_id);
    }

    public function delete(User $user, StaffMember $staffMember): bool
    {
        return $user->can('staff_members.delete')
            && $this->belongsToUserDepartment($user, $staffMember->department_id);
    }
}
