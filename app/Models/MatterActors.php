<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatterActors extends Model
{
    public $timestamps = false;

    public function matter()
    {
        return $this->belongsTo(\App\Models\Matter::class);
    }

    public function actor()
    {
        return $this->belongsTo(\App\Models\Actor::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Actor::class, 'company_id');
    }
}
