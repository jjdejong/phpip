<?php

namespace App\Traits;

use App\Models\Actor;
use App\Models\ActorPivot;
use Illuminate\Support\Facades\DB;

/**
 * Trait HasOneActorThroughActorPivot
 *
 * Provides a method to define a one-to-one relationship through a pivot table.
 */
trait HasOneActorThroughActorPivot
{
    /**
     * Define a one-to-one relationship through a pivot table with a specific role.
     *
     * This method returns a hasOneThrough relationship with the actors table,
     * using the ActorPivot model and filtering by the specified role.
     * If no relationship exists for the given role, it returns a relationship
     * with the container actors that are shared.
     *
     * @param string $role The role to filter the relationship by.
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough The hasOneThrough relationship.
     */
    public function hasOneActorThroughActorPivot(string $role): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        // Define the hasOneThrough relationship with the actors table
        $request = $this->hasOneThrough(Actor::class, ActorPivot::class, 'matter_id', 'id', 'id', 'actor_id')
            ->select('actor.*', 'matter_actor_lnk.*')
            ->where('role', $role);

        // If no relationship exists for the given role, return the container actors that are shared
        if(!$request->exists()) {
            $request = $this->hasOneThrough(Actor::class, ActorPivot::class, 'matter_id', 'id', 'container_id', 'actor_id')
                ->select('actor.*', 'matter_actor_lnk.*')
                ->where('role', $role)
                ->where('shared', 1);
        }

        return $request;
    }
}