<?php

namespace App\Policies;

use App\Matter;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any matters.
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

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

    /**
     * Determine whether the user can create matters.
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->default_role === 'DBRW' || $user->default_role === 'DBA';
    }

    /**
     * Determine whether the user can update the matter.
     *
     * @return mixed
     */
    public function update(User $user, Matter $matter)
    {
        return $user->default_role === 'DBRW' || $user->default_role === 'DBA';
    }

    /**
     * Determine whether the user can delete the matter.
     *
     * @return mixed
     */
    public function delete(User $user, Matter $matter)
    {
        return $user->default_role === 'DBRW' || $user->default_role === 'DBA';
    }
}
