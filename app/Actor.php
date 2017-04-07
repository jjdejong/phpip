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
}
