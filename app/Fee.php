<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $hidden = [ 'creator', 'created_at', 'updated_at', 'updater'];
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function country()
    {
        return $this->belongsTo('App\Country', 'for_country');
    }

    public function category()
    {
        return $this->belongsTo('App\Category', 'for_category', 'code');
    }

    public function origin()
    {
        return $this->belongsTo('App\Country', 'for_origin', 'iso');
    }
}
