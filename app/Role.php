<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'actor_role';
    protected $primaryKey = 'code';
    protected $hidden = ['creator', 'updated', 'updater'];
    protected $guarded = ['code', 'creator', 'updated', 'updater'];
    public $incrementing = false;
    public $timestamps = false;
}
