<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    protected $table = 'actor';
    public $timestamps = false;
    protected $hidden = ['login', 'last_login', 'password', 'remember_token', 'creator', 'updated', 'updater'];
    protected $guarded = ['id', 'password', 'creator', 'updated', 'updater'];

    public function company() {
    	return $this->belongsTo('App\Actor', 'company_id');
    }

    public function parent() {
    	return $this->belongsTo('App\Actor', 'parent_id');
    }

    /*public function matters() {
      return $this->belongsToMany('App\Matter', 'matter_actor_lnk')
      ->withPivot('id', 'role', 'display_order', 'shared', 'actor_ref', 'company_id', 'rate', 'date');
    }*/

    public function matters() {
  		return $this->hasMany('App\ActorPivot');
  	}
}
