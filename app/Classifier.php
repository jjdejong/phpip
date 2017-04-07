<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classifier extends Model
{
    protected $table = 'classifier';
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];
    protected $guarded = ['id', 'creator', 'updated', 'updater'];
    
    public function type()
    {
    	return $this->belongsTo('App\ClassifierType', 'type_code');
    }
    
    public function linkedMatter()
    {
    	return $this->belongsTo('App\Matter', 'lnk_matter_id');
    }
}
