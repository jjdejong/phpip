<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventName extends Model
{
    protected $table = 'event_name';
    protected $primaryKey = 'code';
    public $incrementing = false;
    public $timestamps = false;
}
