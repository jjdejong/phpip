<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslationsExtended;

/**
 * Category Model
 *
 * Represents intellectual property categories such as:
 * - PAT (Patents)
 * - TM (Trademarks)
 * - DS (Designs)
 * - DOM (Domain Names)
 *
 * Database table: matter_category
 *
 * Key relationships:
 * - Has many matters
 * - Can belong to another category for display grouping
 *
 * Business logic:
 * - Categories determine which event types and rules are applicable
 * - Categories can be grouped for display purposes
 * - Category names are translatable for multi-language support
 * - Uses string code as primary key
 */
class Category extends Model
{
    use HasTableComments;
    use HasTranslationsExtended;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'matter_category';

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
    public $translatable = ['category'];

    /**
     * Get all matters in this category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matters()
    {
        return $this->hasMany(Matter::class, 'category_code', 'code');
    }

    /**
     * Get the category this should be displayed with.
     *
     * Used for grouping related categories in navigation and reports.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function displayWithInfo()
    {
        return $this->belongsTo(Category::class, 'display_with', 'code');
    }
}
