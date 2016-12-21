<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Matter extends Model
{
    protected $table = 'matter';
    protected $primaryKey = 'ID'; // necessary because "id" is expected by default and we have "ID"
    public $timestamps = false;
}
