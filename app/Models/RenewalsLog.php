<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * RenewalsLog Model
 *
 * Represents log entries for renewal processing activities.
 * Tracks actions taken on renewal tasks, including:
 * - Renewal payments processed
 * - Renewal instructions sent
 * - Renewal status changes
 * - User actions and timestamps
 *
 * Database table: renewals_logs
 *
 * Key relationships:
 * - Belongs to a matter
 * - Belongs to a renewal task
 * - Belongs to a user (creator of the log entry)
 *
 * Business logic:
 * - Provides audit trail for renewal management
 * - Tracks who did what and when for renewals
 * - Used for renewal reporting and history
 * - Helps ensure renewals are not missed or duplicated
 */
class RenewalsLog extends Model
{
    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = [];

    /**
     * Get the matter this log entry relates to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }

    /**
     * Get the user who created this log entry.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creatorInfo()
    {
        return $this->belongsTo(User::class, 'creator', 'login');
    }

    /**
     * Get the renewal task this log entry relates to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
