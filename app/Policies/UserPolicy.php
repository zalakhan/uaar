<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only super_admin can manage users.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        // Super admin account is not edited via user management
        if ($model->hasRole('super_admin')) {
            return false;
        }

        return $user->can('users.edit');
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id || $model->hasRole('super_admin')) {
            return false;
        }

        return $user->can('users.delete');
    }
}
