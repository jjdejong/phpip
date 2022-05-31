<?php

namespace App\Policies;

use App\Actor;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ActorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->default_role !== 'CLI';
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Actor  $actor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Actor $actor)
    {
        return $user->default_role !== 'CLI';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->default_role === 'DBRW' || $user->default_role === 'DBA';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Actor  $actor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Actor $actor)
    {
        return $user->default_role === 'DBRW' || $user->default_role === 'DBA';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Actor  $actor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Actor $actor)
    {
        return $user->default_role === 'DBRW' || $user->default_role === 'DBA';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Actor  $actor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Actor $actor)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Actor  $actor
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Actor $actor)
    {
        //
    }
}
