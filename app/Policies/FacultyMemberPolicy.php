<?php

namespace App\Policies;

use App\Models\FacultyMember;
use App\Models\User;
use App\Policies\Concerns\ChecksDepartmentAccess;

class FacultyMemberPolicy
{
    use ChecksDepartmentAccess;

    public function viewAny(User $user): bool
    {
        return $user->can('faculty_members.view');
    }

    public function view(User $user, FacultyMember $facultyMember): bool
    {
        if ($facultyMember->member_type !== 'faculty') {
            return false;
        }

        return $user->can('faculty_members.view')
            && $this->canViewDepartmentRecord($user, $facultyMember->department_id);
    }

    public function create(User $user): bool
    {
        return $user->can('faculty_members.create');
    }

    public function update(User $user, FacultyMember $facultyMember): bool
    {
        if ($facultyMember->member_type !== 'faculty') {
            return false;
        }

        return $user->can('faculty_members.edit')
            && $this->belongsToUserDepartment($user, $facultyMember->department_id);
    }

    public function delete(User $user, FacultyMember $facultyMember): bool
    {
        if ($facultyMember->member_type !== 'faculty') {
            return false;
        }

        return $user->can('faculty_members.delete')
            && $this->belongsToUserDepartment($user, $facultyMember->department_id);
    }
}
