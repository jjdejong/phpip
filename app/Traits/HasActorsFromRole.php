<?php

namespace App\Traits;

use App\Models\Actor;
use App\Models\ActorPivot;
use App\Models\MatterActors;

/**
 * Trait for retrieving actors associated with a matter by role.
 *
 * Provides helper methods to fetch actors linked to a matter through specific roles
 * (e.g., client, agent, inventor). Supports both direct actor relationships and
 * inherited relationships from container matters.
 */
trait HasActorsFromRole
{
    /**
     * Get all actors associated with the matter for a specific role.
     *
     * Retrieves actors filtered by role code, ordered by display order.
     * The actors() relationship should be defined in the using model and
     * typically includes both direct and inherited (shared) actor relationships.
     *
     * @param string $role The role code to filter actors by (e.g., 'CLI' for client, 'AGT' for agent).
     * @return \Illuminate\Database\Eloquent\Collection Collection of MatterActors with the specified role.
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
     * Get a single actor associated with the matter for a specific role.
     *
     * Convenience method that returns the first actor matching the specified role,
     * typically used for roles that should have only one actor (e.g., primary client).
     *
     * @param string $role The role code to filter by (e.g., 'CLI' for client, 'AGT' for agent).
     * @return \App\Models\MatterActors|null The first actor with the specified role, or null if none found.
     */
    public function getActorFromRole(string $role): ?MatterActors
    {
        return $this->getActorsFromRole($role)->first();
    }
}