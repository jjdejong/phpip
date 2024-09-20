<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function country()
    {
        return $this->belongsTo(\App\Models\Country::class, 'for_country');
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class, 'for_category', 'code');
    }

    public function origin()
    {
        return $this->belongsTo(\App\Models\Country::class, 'for_origin', 'iso');
    }
}
