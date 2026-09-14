<?php

namespace App\Policies;

use App\Models\CampusPublication;
use App\Models\User;

class CampusPublicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('campus_publications.view');
    }

    public function view(User $user, CampusPublication $campusPublication): bool
    {
        return $user->can('campus_publications.view');
    }

    public function create(User $user): bool
    {
        return $user->can('campus_publications.create');
    }

    public function update(User $user, CampusPublication $campusPublication): bool
    {
        return $user->can('campus_publications.edit');
    }

    public function delete(User $user, CampusPublication $campusPublication): bool
    {
        return $user->can('campus_publications.delete');
    }
}
