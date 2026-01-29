<?php

namespace App\Policies;

use App\Models\Card;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CardPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin') || $user->studio()->exists();
    }

    public function view(User $user, Card $card): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($card->owner_type === \App\Models\Studio::class && $user->studio) {
            return (int)$card->owner_id === (int)$user->studio->studio_id;
        }

        return (int)$user->id === (int)$card->holder_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->studio()->exists();
    }

    public function update(User $user, Card $card): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($card->owner_type === \App\Models\Studio::class && $user->studio) {
            return (int)$card->owner_id === (int)$user->studio->studio_id;
        }

        return false;
    }

    public function delete(User $user, Card $card): bool
    {
        return $user->hasRole('admin');
    }
}
