<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classifier extends Model
{
    protected $table = 'classifier';
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];
    
    public function type()
    {
    	return $this->hasOne('App\ClassifierType');
    }
}
