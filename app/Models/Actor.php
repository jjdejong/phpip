<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

/**
 * Actor Model
 *
 * Represents any entity that can participate in IP matters, including:
 * - Companies and law firms
 * - Individuals (clients, inventors, agents)
 * - Internal users
 * - Contact persons
 *
 * Database table: actor
 *
 * Key relationships:
 * - Can belong to a parent actor (organizational hierarchy)
 * - Can belong to a company
 * - Can belong to a site (physical location)
 * - Has many matters through pivot table
 * - Has a default role defining permissions
 *
 * Business logic:
 * - Actors can have multiple roles in different matters
 * - Supports hierarchical organization structure
 * - Stores contact information including addresses and nationality
 * - Can be both a user (with login) and a contact/company
 */
class Actor extends Model
{
    use HasTableComments;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'actor';

    /**
     * Attributes that should be hidden from serialization.
     *
     * Includes sensitive data like passwords and system fields.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'creator',
        'updater',
        'created_at',
        'updated_at',
        'login'
    ];

    /**
     * Attributes that are not mass assignable.
     *
     * Protects sensitive and system-managed fields.
     *
     * @var array<string>
     */
    protected $guarded = [
        'id',
        'password',
        'remember_token',
        'creator',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'language' => 'string',
    ];
    
    /**
     * Get the actor's preferred language.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language ?? config('app.locale');
    }

    /**
     * Get the company this actor belongs to.
     *
     * Used for organizational hierarchy, linking individuals to their employing company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Actor::class, 'company_id');
    }

    /**
     * Get the parent actor in the organizational hierarchy.
     *
     * Used for creating multi-level organizational structures.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Actor::class, 'parent_id');
    }

    /**
     * Get the site (physical location) this actor is associated with.
     *
     * Used for tracking which office or branch an actor belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function site()
    {
        return $this->belongsTo(Actor::class, 'site_id');
    }

    /**
     * Get all matters associated with this actor.
     *
     * Returns matters through the matter_actor_lnk pivot table.
     * Actor can have various roles in different matters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function matters()
    {
        return $this->belongsToMany(Matter::class, 'matter_actor_lnk');
    }

    /**
     * Get all matter relationships with full pivot data.
     *
     * Returns ActorPivot instances which include role and other relationship details.
     * Useful when you need access to the pivot table attributes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mattersWithLnk()
    {
        return $this->hasMany(ActorPivot::class, 'actor_id');
    }

    /**
     * Get the default role information for this actor.
     *
     * The default role determines the actor's permissions and access level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function droleInfo()
    {
        return $this->belongsTo(Role::class, 'default_role');
    }

    /**
     * Get the country information for this actor's primary address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function countryInfo()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    /**
     * Get the country information for this actor's mailing address.
     *
     * Used when mailing address differs from primary address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country_mailingInfo()
    {
        return $this->belongsTo(Country::class, 'country_mailing');
    }

    /**
     * Get the country information for this actor's billing address.
     *
     * Used when billing address differs from primary address.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country_billingInfo()
    {
        return $this->belongsTo(Country::class, 'country_billing');
    }

    /**
     * Get the nationality country information for this actor.
     *
     * Used primarily for individual actors to track citizenship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nationalityInfo()
    {
        return $this->belongsTo(Country::class, 'nationality');
    }
}
