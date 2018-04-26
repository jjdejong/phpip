<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
  protected $table = 'actor';
  public $timestamps = false;
  protected $hidden = ['login', 'last_login', 'password', 'remember_token', 'creator', 'updated', 'updater'];
  protected $guarded = ['id', 'password', 'creator', 'updated', 'updater'];

  use \Venturecraft\Revisionable\RevisionableTrait;
  protected $revisionEnabled = true;
  protected $revisionCreationsEnabled = true;
  protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
  protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

  public function company() {
  	return $this->belongsTo('App\Actor', 'company_id');
  }

  public function parent() {
  	return $this->belongsTo('App\Actor', 'parent_id');
  }

  public function matters() {
		return $this->hasMany('App\ActorPivot');
	}
}
