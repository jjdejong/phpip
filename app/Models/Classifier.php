<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Classifier Model
 *
 * Represents classifications and metadata attached to matters, including:
 * - Titles (invention titles in various languages)
 * - IPC/CPC classes (technical classification)
 * - Keywords and tags
 * - References to other matters
 *
 * Database table: classifier
 *
 * Key relationships:
 * - Belongs to a matter
 * - Belongs to a classifier type (defines the kind of classification)
 * - Can link to another matter (for references)
 *
 * Business logic:
 * - Classifiers can be inherited from container to family members
 * - Classifiers automatically touch (update timestamp of) their parent matter
 * - Some classifier types are displayed prominently (main_display)
 * - Classifiers can link matters together for reference tracking
 */
class Classifier extends Model
{
    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'classifier';

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
     * Updates the matter's timestamp when a classifier changes.
     *
     * @var array<string>
     */
    protected $touches = ['matter'];

    /**
     * Get the classifier type information.
     *
     * Returns the ClassifierType defining what kind of classification this is.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(ClassifierType::class, 'type_code');
    }

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
}
