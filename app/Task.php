<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'task';
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];

    public function info() 
    {
		return $this->hasOne('App\EventName', 'code');
	}
}
