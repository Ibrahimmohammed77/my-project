<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlbumPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Album $album): bool
    {
        return $user->id === $album->user_id || $user->hasRole('admin');
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Album $album): bool
    {
        return $user->id === $album->user_id || $user->hasRole('admin');
    }

    public function delete(User $user, Album $album): bool
    {
        return $user->id === $album->user_id || $user->hasRole('admin');
    }
}
