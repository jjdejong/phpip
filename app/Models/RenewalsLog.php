<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RenewalsLog extends Model
{
    protected $guarded = [];

    /**
     * Get the matter of the log line.
     */
    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }

    /**
     * Get the matter of the creator.
     */
    public function creatorInfo()
    {
        return $this->belongsTo(User::class, 'creator', 'login');
    }

    /**
     * Get the matter of the renewal task.
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
