<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MatterClassifiers extends Model
{
    protected $table = 'matter_classifiers';
    public $timestamps = false;

    public function linkedMatter()
    {
    	return $this->belongsTo(\App\Matter::class, 'lnk_matter_id');
    }

    public function matter()
    {
    	return $this->belongsTo(\App\Matter::class);
    }
}
