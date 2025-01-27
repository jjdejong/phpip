<?php

namespace App\Traits;

use App\Models\Actor;
use App\Models\ActorPivot;

trait BelongsToManyActors
{
    /**
     * Define a belongsToMany relationship with the Actor model, including container relations.
     *
     * This method returns a belongsToMany relationship with the actors table,
     * using the ActorPivot model and including additional pivot fields.
     * If no relationship exists for the given role, it returns a relationship
     * with the container actors that are shared.
     *
     * @param string $role The role to filter the actors by.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany The belongsToMany relationship.
     */
    public function belongsToManyActors(string $role)
    {
        // Define the belongsToMany relationship with the actors table
        $request = $this->belongsToMany(Actor::class, 'matter_actor_lnk', 'matter_id', 'actor_id')
            ->using(ActorPivot::class)
            ->withPivot('role', 'display_order', 'shared', 'actor_ref')
            ->wherePivot('role', $role);

        // If no relationship exists for the given role, return the container actors that are shared
        if(!$request->exists()) {
            return $this->belongsToMany(Actor::class, 'matter_actor_lnk', 'matter_id', 'actor_id', 'container_id')
                ->using(ActorPivot::class)
                ->withPivot('role', 'display_order', 'shared', 'actor_ref')
                ->wherePivot('role', $role)
                ->wherePivot('shared', 1);
        }

        return $request;
    }
}