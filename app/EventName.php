<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EventName extends Model
{
    protected $table = 'event_name';
    protected $primaryKey = 'code';
    protected $hidden = ['creator', 'updated', 'updater'];
    protected $guarded = ['id', 'creator', 'updated', 'updater'];
    public $incrementing = false;
    public $timestamps = false;
    
    public function events()
    {
    	return $this->hasMany('App\Event', 'code');
    }
    
    public function tasks()
    {
    	return $this->hasMany('App\Task', 'code');
    }

	public function countryInfo()
	{
		return $this->belongsTo('App\Country', 'country','iso');
	}

	public function categoryInfo()
	{
		return $this->belongsTo('App\Category','category','code');
	}
	
	public function default_responsibleInfo()
	{
		return $this->belongsTo('App\User','default_responsible','login');
	}
}
