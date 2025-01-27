<?php

namespace App\Traits;

use App\Models\Actor;
use App\Models\ActorPivot;

trait HasActorsFromRole
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
     * @return
     */
    public function getActorsFromRole(string $role): \Illuminate\Database\Eloquent\Collection
    {
        return Actor::query()
            ->join('matter_actor_lnk', 'actor.id', '=', 'matter_actor_lnk.actor_id')
            ->where('matter_actor_lnk.role', $role)
            ->where(function ($query) {
                $query
                    ->where('matter_actor_lnk.matter_id', $this->id)
                    ->orWhere('matter_actor_lnk.matter_id', $this->container_id);
            })
            ->select('actor.*', 'matter_actor_lnk.*')
            ->get();
    }

    /**
     * Define a one-to-one relationship through a pivot table with a specific role.
     *
     * This method returns a hasOneThrough relationship with the actors table,
     * using the ActorPivot model and filtering by the specified role.
     * If no relationship exists for the given role, it returns a relationship
     * with the container actors that are shared.
     *
     * @param string $role The role to filter the relationship by.
     * @return \App\Models\Actor|null The hasOneThrough relationship.
     */
    public function getActorFromRole(string $role): ?Actor
    {
        return $this->getActorsFromRole($role)->first();
    }
}