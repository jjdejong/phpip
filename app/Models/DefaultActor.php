<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultActor extends Model
{
    protected $table = 'default_actor';

    protected $guarded = ['created_at', 'updated_at'];

    public function actor()
    {
        return $this->belongsTo(\App\Models\Actor::class);
    }

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class, 'for_country', 'iso');
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'for_category', 'code');
    }

    public function client()
    {
        return $this->belongsTo(\App\Models\Actor::class, 'for_client');
    }

    public function roleInfo()
    {
        return $this->belongsTo(\App\Models\Role::class, 'role', 'code');
    }
}
