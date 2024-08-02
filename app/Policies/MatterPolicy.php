<?php

namespace App\Policies;

use App\Matter;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the matter.
     *
     * @return mixed
     */
    public function view(User $user, Matter $matter)
    {
        if ($user->default_role === 'CLI') {
            if ($matter->client->count()) {
                return $user->id === $matter->client->actor_id;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
}
