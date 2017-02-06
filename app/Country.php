<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
	protected $table = 'country';
	protected $primaryKey = 'iso';
	public $incrementing = false;
	public $timestamps = false;
	protected $hidden = ['name_DE', 'name_FR', 'iso3', 'numcode'];
	
	public function getGoesnationalAttribute() { // Defines "goesnational" as an attribute
		return in_array($this->iso, ['EP', 'WO', 'EM', 'OA']);
	}
}
