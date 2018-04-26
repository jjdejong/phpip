<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ActorPivot extends Model
{
  protected $table = 'matter_actor_lnk';
  public $timestamps = false;
  protected $hidden = ['creator', 'updated', 'updater'];
  protected $guarded = ['id', 'creator', 'updated', 'updater'];

  use \Venturecraft\Revisionable\RevisionableTrait;
  protected $revisionEnabled = true;
  protected $revisionCreationsEnabled = true;
  protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
  protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

  public function matter() {
		return $this->belongsTo('App\Matter');
	}

  public function actor() {
		return $this->belongsTo('App\Actor');
	}

  public function role() {
		return $this->belongsTo('App\Role', 'role');
	}
}
