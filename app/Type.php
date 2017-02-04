<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'matter_type';
    protected $primaryKey = 'code';
    public $incrementing = false;
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];
}
