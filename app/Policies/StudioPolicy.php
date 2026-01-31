<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Studio;

class StudioPolicy
{
    /**
     * Determine if the user can view any studios.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_studios') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can view the studio.
     */
    public function view(User $user, Studio $studio): bool
    {
        return $user->hasPermission('view_studios') || 
               ($user->studio && $user->studio->studio_id === $studio->studio_id) ||
               $user->hasRole('admin');
    }

    /**
     * Determine if the user can create studios.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage_studios') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the studio.
     */
    public function update(User $user, Studio $studio): bool
    {
        return $user->hasPermission('manage_studios') || 
               ($user->studio && $user->studio->studio_id === $studio->studio_id) ||
               $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the studio.
     */
    public function delete(User $user, Studio $studio): bool
    {
        return $user->hasPermission('manage_studios') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can manage studio resources.
     */
    public function manage(User $user, Studio $studio): bool
    {
        return ($user->studio && $user->studio->studio_id === $studio->studio_id) ||
               $user->hasRole('admin');
    }
}
