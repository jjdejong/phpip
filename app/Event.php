<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'event';
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];

    public function info() 
    {
		return $this->hasOne('App\EventName', 'code');
	}

	public function link() 
	{
		return $this->hasOne('App\Event', 'matter_id', 'alt_matter_id')->where('code', 'FIL');
	}
}
