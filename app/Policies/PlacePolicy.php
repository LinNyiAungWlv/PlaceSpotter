<?php

namespace App\Policies;

use App\Models\Place;
use App\Models\User;

class PlacePolicy
{
    // only the person who shared a place can edit or delete it
    public function update(User $user, Place $place): bool
    {
        return $user->id === $place->user_id;
    }

    public function delete(User $user, Place $place): bool
    {
        return $user->id === $place->user_id;
    }
}
