<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

/**
 * DefaultActor Model
 *
 * Represents default actor assignments for automatic population when creating new matters.
 * Allows defining standard actors (agents, attorneys, etc.) based on:
 * - Country
 * - Category (patent, trademark, etc.)
 * - Client
 * - Role (what function the actor performs)
 *
 * Database table: default_actor
 *
 * Key relationships:
 * - Belongs to an actor (the default actor to use)
 * - Belongs to a country (scope of applicability)
 * - Belongs to a category (scope of applicability)
 * - Belongs to a client (client-specific defaults)
 * - Belongs to a role (what function the actor performs)
 *
 * Business logic:
 * - Used to auto-populate actors when creating new matters
 * - Can be scoped by country, category, client, or combinations
 * - Most specific match takes precedence (client+country+category > country+category > category)
 * - Speeds up matter creation by pre-filling standard actors
 * - Reduces data entry errors by ensuring consistent actor assignments
 */
class DefaultActor extends Model
{
    use HasTableComments;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'default_actor';

    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Get the actor to use as default.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    /**
     * Get the country this default applies to.
     *
     * Null means applies to all countries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'for_country', 'iso');
    }

    /**
     * Get the category this default applies to.
     *
     * Null means applies to all categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'for_category', 'code');
    }

    /**
     * Get the client this default applies to.
     *
     * Null means applies to all clients.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Actor::class, 'for_client');
    }

    /**
     * Get the role information for this default.
     *
     * Defines what function the default actor will perform (agent, attorney, etc.).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function roleInfo()
    {
        return $this->belongsTo(Role::class, 'role', 'code');
    }
}
