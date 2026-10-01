<?php

namespace App\Policies;

use App\Models\Newspaper;
use App\Models\User;

class NewspaperPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('newsprint.view');
    }

    public function view(User $user, Newspaper $newspaper): bool
    {
        return $user->can('newsprint.view');
    }

    public function create(User $user): bool
    {
        return $user->can('newsprint.create');
    }

    public function update(User $user, Newspaper $newspaper): bool
    {
        return $user->can('newsprint.edit');
    }

    public function delete(User $user, Newspaper $newspaper): bool
    {
        return $user->can('newsprint.delete');
    }
}
