<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'actor_role';
    protected $primaryKey = 'code';
    public $incrementing = false;
    public $timestamps = false;
}
