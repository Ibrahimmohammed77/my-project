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
        if ($user->hasRole('admin') || $user->hasRole('super_admin')) {
            return true;
        }

        // Check if user owns the studio/school that owns the album
        if ($album->owner_type === \App\Models\Studio::class) {
            return $user->studio && $user->studio->studio_id === $album->owner_id;
        }

        if ($album->owner_type === \App\Models\School::class) {
            return $user->school && $user->school->school_id === $album->owner_id;
        }

        return $user->id === $album->owner_id && $album->owner_type === User::class;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('studio_owner') || $user->hasRole('school_owner') || $user->hasRole('admin');
    }

    public function update(User $user, Album $album): bool
    {
        return $this->view($user, $album);
    }

    public function delete(User $user, Album $album): bool
    {
        return $this->view($user, $album);
    }
}
