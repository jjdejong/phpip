<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'matter_category';
    protected $primaryKey = 'code';
    public $incrementing = false;
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];
}
