<?php

namespace App\Policies;

use App\Models\User;
use App\Models\School;

class SchoolPolicy
{
    /**
     * Determine if the user can view any schools.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_schools') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can view the school.
     */
    public function view(User $user, School $school): bool
    {
        return $user->hasPermission('view_schools') || 
               ($user->school && $user->school->school_id === $school->school_id) ||
               $user->hasRole('admin');
    }

    /**
     * Determine if the user can create schools.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage_schools') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the school.
     */
    public function update(User $user, School $school): bool
    {
        return $user->hasPermission('manage_schools') || 
               ($user->school && $user->school->school_id === $school->school_id) ||
               $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the school.
     */
    public function delete(User $user, School $school): bool
    {
        return $user->hasPermission('manage_schools') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can manage school resources.
     */
    public function manage(User $user, School $school): bool
    {
        return ($user->school && $user->school->school_id === $school->school_id) ||
               $user->hasRole('admin');
    }
}
