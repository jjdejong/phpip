<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * ActorPivot Model
 *
 * Represents the pivot table for the many-to-many relationship between Matter and Actor models.
 * This extended pivot model stores additional relationship data including:
 * - Role of the actor in the matter (client, agent, inventor, etc.)
 * - Display order for multiple actors in same role
 * - Reference numbers (actor's own reference for the matter)
 * - Company affiliation
 * - Whether the relationship is shared with family members
 *
 * Database table: matter_actor_lnk
 *
 * Key relationships:
 * - Belongs to a matter
 * - Belongs to an actor
 * - Belongs to a role (defines actor's function)
 * - Belongs to a company (actor's employer)
 *
 * Business logic:
 * - Pivot records automatically touch the matter to update timestamps
 * - Display order allows sorting actors in the same role
 * - Shared actors are inherited by family members
 * - Each actor can have multiple roles in the same matter
 */
class ActorPivot extends Pivot
{
    /**
     * The database table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'matter_actor_lnk';

    /**
     * Attributes that should be hidden from serialization.
     *
     * @var array<string>
     */
    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Related models that should be touched when this model is updated.
     *
     * Updates the matter's timestamp when actor relationships change.
     *
     * @var array<string>
     */
    protected $touches = ['matter'];

    /**
     * Get the matter that owns the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Matter::class, 'matter_id');
    }

    /**
     * Get the actor that owns the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Actor::class, 'actor_id');
    }

    /**
     * Get the role associated with the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class, 'role');
    }

    /**
     * Get the company associated with the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Actor::class, 'company_id');
    }
}