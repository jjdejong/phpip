<?php

namespace App\Policies;

use App\Rule;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RulePolicy
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
     * @param  \App\Rule  $rule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Rule $rule)
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
        return $user->default_role === 'DBA';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Rule  $rule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Rule $rule)
    {
        return $user->default_role === 'DBA';
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Rule  $rule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Rule $rule)
    {
        return $user->default_role === 'DBA';
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Rule  $rule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Rule $rule)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Rule  $rule
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Rule $rule)
    {
        //
    }
}
