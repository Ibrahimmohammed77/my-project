<?php

namespace App\Policies;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhotoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Photo $photo): bool
    {
        return $user->id === $photo->album->user_id || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Photo $photo): bool
    {
        return $user->id === $photo->album->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, Photo $photo): bool
    {
        return $user->id === $photo->album->user_id || $user->hasRole('admin');
    }
}
