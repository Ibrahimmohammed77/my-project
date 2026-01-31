<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Subscription;

class SubscriptionPolicy
{
    /**
     * Determine if the user can view any subscriptions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_subscriptions') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can view the subscription.
     */
    public function view(User $user, Subscription $subscription): bool
    {
        return $user->hasPermission('view_subscriptions') || 
               $user->id === $subscription->user_id ||
               $user->hasRole('admin');
    }

    /**
     * Determine if the user can create subscriptions.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('create_subscriptions') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can update the subscription.
     */
    public function update(User $user, Subscription $subscription): bool
    {
        return $user->hasPermission('update_subscriptions') || 
               $user->id === $subscription->user_id ||
               $user->hasRole('admin');
    }

    /**
     * Determine if the user can delete the subscription.
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        return $user->hasPermission('delete_subscriptions') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can grant subscriptions to others.
     */
    public function grant(User $user): bool
    {
        return $user->hasPermission('grant_subscriptions') || $user->hasRole('admin');
    }
}
