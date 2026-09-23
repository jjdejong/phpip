<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslationsExtended;

/**
 * MatterActors Model
 *
 * Represents a database view that combines matter-actor relationships with inherited relationships
 * from containers. This view provides a unified view of all actors associated with a matter,
 * including those directly linked and those inherited from parent containers.
 *
 * Database table: matter_actors (view)
 *
 * Key relationships:
 * - Belongs to a matter
 * - Belongs to an actor
 * - Belongs to a role
 * - Belongs to a company
 *
 * Business logic:
 * - This is a READ-ONLY view model - do not use for inserts/updates
 * - Automatically includes actors inherited from container matters
 * - Role names are translatable for multi-language support
 * - No timestamps (view-based model)
 * - Used primarily for displaying actor information
 */
class MatterActors extends Model
{
    use HasTranslationsExtended;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Attributes that support multi-language translations.
     *
     * @var array<string>
     */
    public $translatable = ['role_name'];

    /**
     * Limit a set of client links to those a client user may see.
     *
     * This is the one definition of client visibility: the link is visible when
     * its actor is the user, is the user's client company, or is employed by
     * that company. Matter::whereVisibleToClient() mirrors it for the hand-built
     * joins in Matter::filter(), which cannot use a relation.
     *
     * Callers must have established that the user is a client - internal users
     * are never filtered through this.
     *
     * The actor ids are resolved first and passed as a literal list. matter_actors
     * is a UNION view, and MySQL only pushes a plain "actor_id IN (...)" down into
     * it: an OR with a subquery made it rebuild the whole view for every row of
     * the outer query - 26 s instead of 0.1 s for the dashboard task counts.
     */
    public function scopeForClientUser(Builder $query, User $user): void
    {
        $query->whereIn('actor_id', $user->clientActorIds());
    }

    /**
     * Get the matter this actor relationship belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }

    /**
     * Get the actor in this relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    /**
     * Get the role information for this actor-matter relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_code');
    }

    /**
     * Get the company the actor is affiliated with.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Actor::class, 'company_id');
    }
}
