<?php

namespace App\Policies;

use App\Models\Tender;
use App\Models\User;

class TenderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('tenders.view');
    }

    public function view(User $user, Tender $tender): bool
    {
        return $user->can('tenders.view');
    }

    public function create(User $user): bool
    {
        return $user->can('tenders.create');
    }

    public function update(User $user, Tender $tender): bool
    {
        return $user->can('tenders.edit');
    }

    public function delete(User $user, Tender $tender): bool
    {
        return $user->can('tenders.delete');
    }
}
