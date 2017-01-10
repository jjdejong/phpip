<?php

namespace App\Policies;

use App\User;
use App\Matter;
use Illuminate\Auth\Access\HandlesAuthorization;

class MatterPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the matter.
     *
     * @param  \App\User  $user
     * @param  \App\Matter  $matter
     * @return mixed
     */
    public function view(User $user, Matter $matter)
    {
        return $user->default_role == 'DBA';
    }

    /**
     * Determine whether the user can create matters.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the matter.
     *
     * @param  \App\User  $user
     * @param  \App\Matter  $matter
     * @return mixed
     */
    public function update(User $user, Matter $matter)
    {
        //
    }

    /**
     * Determine whether the user can delete the matter.
     *
     * @param  \App\User  $user
     * @param  \App\Matter  $matter
     * @return mixed
     */
    public function delete(User $user, Matter $matter)
    {
        //
    }
}
