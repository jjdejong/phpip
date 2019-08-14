<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'matter_type';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];
    protected $guarded = ['code', 'creator', 'created_at', 'updated_at', 'updater'];
}
