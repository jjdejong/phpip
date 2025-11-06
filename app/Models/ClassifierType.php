<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslationsExtended;

/**
 * ClassifierType Model
 *
 * Defines types of classifiers that can be attached to matters, such as:
 * - TIT (Title)
 * - TITOF (Official Title)
 * - TITEN (English Title)
 * - IPC (International Patent Classification)
 * - CPC (Cooperative Patent Classification)
 * - KEYWORDS (Keywords/Tags)
 * - LINK (Links to other matters)
 *
 * Database table: classifier_type
 *
 * Key relationships:
 * - Belongs to a category (classifier types can be category-specific)
 *
 * Business logic:
 * - Classifier types control what kinds of metadata can be added to matters
 * - Some types are marked as "main_display" for prominent display
 * - Types have display order for sorting in UI
 * - Type names are translatable for multi-language support
 * - Uses string code as primary key
 */
class ClassifierType extends Model
{
    use HasTableComments;
    use HasTranslationsExtended;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'classifier_type';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'code';

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

    /**
     * Get the category this classifier type applies to.
     *
     * Classifier types can be category-specific or apply to all categories if null.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'for_category', 'code');
    }
}
