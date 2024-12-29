<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatterActors extends Model
{
    public $timestamps = false;

    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }

    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_code');
    }

    public function company()
    {
        return $this->belongsTo(Actor::class, 'company_id');
    }
}
