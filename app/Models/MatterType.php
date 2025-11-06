<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslationsExtended;

/**
 * MatterType Model
 *
 * Represents types of matters within categories, providing additional classification such as:
 * - Patent types (utility, design, plant)
 * - Trademark types (word mark, figurative mark, sound mark)
 * - Application types (provisional, non-provisional, PCT)
 *
 * Database table: matter_type
 *
 * Key relationships:
 * - Used by matters for additional classification
 * - Used by rules for type-specific deadline calculations
 *
 * Business logic:
 * - Types provide finer-grained classification within categories
 * - Type names are translatable for multi-language support
 * - Uses string code as primary key
 * - Types can affect which rules and events are applicable
 */
class MatterType extends Model
{
    use HasTableComments;
    use HasTranslationsExtended;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'matter_type';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

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
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Attributes that support multi-language translations.
     *
     * @var array<string>
     */
    public $translatable = ['type'];
}
