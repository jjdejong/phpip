<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

/**
 * Fee Model
 *
 * Represents official fees and agent costs for renewals and other proceedings.
 * Stores fee schedules for different countries, categories, and years/quantities.
 *
 * Database table: fees
 *
 * Key relationships:
 * - Belongs to a country (jurisdiction where fee applies)
 * - Belongs to a category (type of IP)
 * - Belongs to an origin country (for origin-dependent fees)
 *
 * Business logic:
 * - Used primarily for renewal fee calculation
 * - Can have different fee levels (standard, reduced, supplementary)
 * - Fees vary by year/quantity (annuity number, trademark classes, etc.)
 * - Can be country, category, and origin-specific
 * - Includes both official fees and agent costs
 */
class Fee extends Model
{
    use HasTableComments;

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
     * Get the country this fee applies to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'for_country');
    }

    /**
     * Get the category this fee applies to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'for_category', 'code');
    }

    /**
     * Get the origin country this fee applies to.
     *
     * Some countries have different fees based on application origin.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function origin()
    {
        return $this->belongsTo(Country::class, 'for_origin', 'iso');
    }
}
