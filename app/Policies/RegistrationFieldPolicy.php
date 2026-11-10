<?php

namespace App\Policies;

use App\Models\RegistrationField;
use App\Models\User;

class RegistrationFieldPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('registration_fields.view');
    }

    public function view(User $user, RegistrationField $registrationField): bool
    {
        return $user->can('registration_fields.view');
    }

    public function create(User $user): bool
    {
        return $user->can('registration_fields.create');
    }

    public function update(User $user, RegistrationField $registrationField): bool
    {
        return $user->can('registration_fields.edit');
    }

    public function delete(User $user, RegistrationField $registrationField): bool
    {
        return $user->can('registration_fields.delete');
    }
}
