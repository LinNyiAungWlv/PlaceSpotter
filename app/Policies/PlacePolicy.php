<?php

namespace App\Policies;

use App\Models\Place;
use App\Models\User;

// Laravel finds this policy automatically because of its name (Place -> PlacePolicy).
// Guests are always refused because these methods need a logged in User.
class PlacePolicy
{
    // only the person who added the place can edit it
    public function update(User $user, Place $place): bool
    {
        return $user->id === $place->user_id;
    }

    // only the person who added the place can delete it
    public function delete(User $user, Place $place): bool
    {
        return $user->id === $place->user_id;
    }
}
