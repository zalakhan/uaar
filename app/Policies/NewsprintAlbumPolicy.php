<?php

namespace App\Policies;

use App\Models\NewsprintAlbum;
use App\Models\User;

class NewsprintAlbumPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('newsprint.view');
    }

    public function view(User $user, NewsprintAlbum $newsprintAlbum): bool
    {
        return $user->can('newsprint.view');
    }

    public function create(User $user): bool
    {
        return $user->can('newsprint.create');
    }

    public function update(User $user, NewsprintAlbum $newsprintAlbum): bool
    {
        return $user->can('newsprint.edit');
    }

    public function delete(User $user, NewsprintAlbum $newsprintAlbum): bool
    {
        return $user->can('newsprint.delete');
    }
}
