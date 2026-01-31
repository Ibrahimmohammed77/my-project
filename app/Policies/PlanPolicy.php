<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Plan;

class PlanPolicy
{
    /**
     * Determine if the user can view any plans.
     */
    public function viewAny(User $user): bool
    {
        return true; // Plans are viewable by all authenticated users
    }

    /**
     * Determine if the user can view the plan.
     */
    public function view(User $user, Plan $plan): bool
    {
        return true; // Plans are viewable by all authenticated users
    }

    /**
     * Determine if the user can create plans.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage_plans') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the plan.
     */
    public function update(User $user, Plan $plan): bool
    {
        return $user->hasPermission('manage_plans') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the plan.
     */
    public function delete(User $user, Plan $plan): bool
    {
        return $user->hasPermission('manage_plans') || $user->hasRole('admin');
    }
}
