<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Event extends Model
{
    protected $table = 'event';
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];

    public function info() 
    {
		return $this->belongsTo('App\EventName', 'code');
	}
	
	public function matter()
	{
		return $this->belongsTo('App\Matter');
	}

	public function link() 
	{
		return $this->hasOne('App\Event', 'matter_id', 'alt_matter_id')->where('code', 'FIL');
	}
	
	public function retroLink()
	{
		return $this->belongsTo('App\Event', 'matter_id', 'alt_matter_id');
	}
	
	public function tasks()
	{
		return $this->hasMany('App\Task', 'trigger_id')
			->orderBy('due_date');
	}
}
