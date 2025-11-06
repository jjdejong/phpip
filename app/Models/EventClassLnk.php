<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * EventClassLnk Model
 *
 * Represents the pivot table linking event names to document template classes.
 * This allows associating document templates with specific event types for
 * automatic document generation.
 *
 * Database table: event_class_lnk
 *
 * Key relationships:
 * - Links an event name to a template class
 *
 * Business logic:
 * - Used to determine which document templates are available for an event/task
 * - Enables context-aware document generation
 * - Simple pivot table with no additional attributes
 */
class EventClassLnk extends Model
{
    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_class_lnk';

    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];

    /**
     * Get the template class this link refers to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo(TemplateClass::class, 'template_class_id');
    }
}
