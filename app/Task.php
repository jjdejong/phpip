<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  protected $table = 'task';
  public $timestamps = false;
  protected $hidden = ['creator', 'updated', 'updater'];
  protected $guarded = ['id', 'creator', 'updated', 'updater'];

  public function info()
  {
		return $this->belongsTo('App\EventName', 'code');
	}

	public function trigger()
	{
		return $this->belongsTo('App\Event', 'trigger_id');
	}
}
