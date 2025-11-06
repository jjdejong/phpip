<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslationsExtended;

/**
 * Country Model
 *
 * Represents countries and regional IP offices, including:
 * - Individual countries (US, GB, FR, etc.)
 * - Regional offices (EP, WO, EM, OA for EPO, WIPO, EUIPO, OAPI)
 *
 * Database table: country
 *
 * Key relationships:
 * - Used by matters for jurisdiction and origin
 * - Used by actors for nationality and addresses
 * - Used by rules for country-specific deadline calculations
 *
 * Business logic:
 * - Uses ISO 2-letter code as primary key
 * - Country names are translatable for multi-language support
 * - Regional offices have special "goesnational" flag
 * - Tracks which countries are designated states for regional offices
 * - No timestamps (reference data)
 */
class Country extends Model
{
    use HasTranslationsExtended;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'country';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'iso';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Attributes that should be hidden from serialization.
     *
     * @var array<string>
     */
    protected $hidden = ['iso3', 'numcode'];

    /**
     * Attributes that support multi-language translations.
     *
     * @var array<string>
     */
    public $translatable = ['name'];

    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];

    /**
     * Accessor to determine if this is a regional office that goes national.
     *
     * Regional offices like EP (EPO), WO (WIPO), EM (EUIPO), and OA (OAPI)
     * require subsequent national phase entries in member countries.
     *
     * @return bool True if this is a regional office requiring national phase
     */
    public function getGoesnationalAttribute() // Defines "goesnational" as an attribute
    {
        return in_array($this->iso, ['EP', 'WO', 'EM', 'OA']);
    }

    /**
     * Accessor to get the list of member countries for a regional office.
     *
     * Returns country names and codes where this regional office has authority.
     * Only applicable for regional offices (goesnational = true).
     *
     * @return \Illuminate\Support\Collection|null Collection of countries or null
     */
    public function getNatcountriesAttribute()
    {
        if ($this->goesnational) {
            return $this->where("$this->iso", 1)->pluck('name', 'iso');
        } else {
            return null;
        }
    }
}
