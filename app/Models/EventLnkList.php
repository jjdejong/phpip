<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * EventLnkList Model
 *
 * Represents a database view that provides a flattened list of event links.
 * This view simplifies querying relationships between events and matters,
 * particularly for priority claims and parent-child relationships.
 *
 * Database table: event_lnk_list (view)
 *
 * Key relationships:
 * - Belongs to a matter
 *
 * Business logic:
 * - This is a READ-ONLY view model - do not use for inserts/updates
 * - Provides easy access to event linking information
 * - Commonly used for displaying priority chains
 * - Event dates are automatically cast to Carbon instances
 */
class EventLnkList extends Model
{
    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_lnk_list';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'event_date' => 'date',
    ];

    /**
     * Get the matter associated with this event link.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }
}
