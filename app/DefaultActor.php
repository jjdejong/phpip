<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class DefaultActor extends Model
{
    protected $table = 'default_actor';
    protected $guarded = ['created_at', 'updated_at'];
 
    public function actor() {
        return $this->belongsTo('App\Actor');
    }
    
    public function country() {
		return $this->belongsTo('App\Country', 'for_country','iso');
	}

    public function category() {
		return $this->belongsTo('App\Category', 'for_category','code');
	}

    public function client() {
        return $this->belongsTo('App\Actor','for_client');
    }

    public function roleInfo() {
        return $this->belongsTo('App\Role','role','code');
    }
}
