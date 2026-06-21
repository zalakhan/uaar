<?php

namespace App\Policies;

use App\Models\Faculty;
use App\Models\User;

class FacultyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('faculties.view');
    }

    public function view(User $user, Faculty $faculty): bool
    {
        return $user->can('faculties.view');
    }

    public function create(User $user): bool
    {
        return $user->can('faculties.create');
    }

    public function update(User $user, Faculty $faculty): bool
    {
        return $user->can('faculties.edit');
    }

    public function delete(User $user, Faculty $faculty): bool
    {
        return $user->can('faculties.delete');
    }
}
