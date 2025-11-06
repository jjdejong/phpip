<?php

namespace App\Models;

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
