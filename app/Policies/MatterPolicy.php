<?php

namespace App\Policies;

use App\Models\Matter;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the matter.
     *
     * Client users may view a matter when one of their client company's actors
     * is linked to it as client, directly or inherited from the container.
     * Everyone else may view every matter.
     *
     * @return mixed
     */
    public function view(User $user, Matter $matter)
    {
        if (! $user->isClient()) {
            return true;
        }

        return $matter->clients()
            ->forClientUser($user)
            ->exists();
    }
}
