<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventLnkList extends Model
{
    protected $table = 'event_lnk_list';

    protected $casts = [
        'event_date' => 'date',
    ];

    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }
}