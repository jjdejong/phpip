<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MatterActors extends Model
{
    protected $table = 'matter_actors';
    public $timestamps = false;

    public function matter()
    {
        return $this->belongsTo(\App\Matter::class);
    }

    public function actor()
    {
        return $this->belongsTo(\App\Actor::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Actor::class, 'company_id');
    }
}
