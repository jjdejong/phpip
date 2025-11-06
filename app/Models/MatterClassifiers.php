<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslationsExtended;

/**
 * MatterClassifiers Model
 *
 * Represents a database view that combines classifiers with inherited classifiers from containers.
 * This view provides a unified view of all classifiers associated with a matter, including
 * those directly attached and those inherited from parent containers.
 *
 * Database table: matter_classifiers (view)
 *
 * Key relationships:
 * - Belongs to a matter
 * - Belongs to a classifier type
 * - Can link to another matter (for reference classifiers)
 *
 * Business logic:
 * - This is a READ-ONLY view model - do not use for inserts/updates
 * - Automatically includes classifiers inherited from container matters
 * - Type names are translatable for multi-language support
 * - No timestamps (view-based model)
 * - Used primarily for displaying classifier information
 * - Includes titles, IPC classes, keywords, and other classifications
 */
class MatterClassifiers extends Model
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
    public $translatable = ['type_name'];

    /**
     * Get the matter this classifier links to.
     *
     * Used when the classifier is a reference to another matter.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function linkedMatter()
    {
        return $this->belongsTo(Matter::class, 'lnk_matter_id');
    }

    /**
     * Get the matter this classifier belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }

    /**
     * Get the classifier type information.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classifierType()
    {
        return $this->belongsTo(ClassifierType::class, 'type_code', 'code');
    }
}
