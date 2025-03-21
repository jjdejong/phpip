<?php

namespace App\Traits;

use App\Models\Actor;
use App\Models\ActorPivot;
use App\Models\MatterActors;

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
        return $this->actors()
            ->with('actor')
            ->where('role_code', $role)
            ->orderBy('display_order')
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
    public function getActorFromRole(string $role): ?MatterActors
    {
        return $this->getActorsFromRole($role)->first();
    }
}