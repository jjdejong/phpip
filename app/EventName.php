<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventName extends Model
{
    protected $table = 'event_name';
    protected $primaryKey = 'code';
    protected $keyType = 'string';
    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];
    protected $guarded = ['created_at', 'updated_at'];
    public $incrementing = false;

    public function events() {
        return $this->hasMany('App\Event', 'code');
    }

    public function tasks() {
        return $this->hasMany('App\Task', 'code');
    }

    public function countryInfo() {
        return $this->belongsTo('App\Country', 'country', 'iso');
    }

    public function categoryInfo() {
        return $this->belongsTo('App\Category', 'category', 'code');
    }

    public function default_responsibleInfo() {
        return $this->belongsTo('App\User', 'default_responsible', 'login');
    }

}
