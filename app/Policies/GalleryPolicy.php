<?php

namespace App\Policies;

use App\Models\Gallery;
use App\Models\User;
use App\Policies\Concerns\ChecksDepartmentAccess;

class GalleryPolicy
{
    use ChecksDepartmentAccess;

    public function viewAny(User $user): bool
    {
        return $user->can('galleries.view');
    }

    public function view(User $user, Gallery $gallery): bool
    {
        return $user->can('galleries.view')
            && $this->canViewDepartmentRecord($user, $gallery->department_id);
    }

    public function create(User $user): bool
    {
        return $user->can('galleries.create');
    }

    public function update(User $user, Gallery $gallery): bool
    {
        return $user->can('galleries.edit')
            && $this->belongsToUserDepartment($user, $gallery->department_id);
    }

    public function delete(User $user, Gallery $gallery): bool
    {
        return $user->can('galleries.delete')
            && $this->belongsToUserDepartment($user, $gallery->department_id);
    }
}
