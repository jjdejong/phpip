<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'matter_category';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];
    protected $guarded = ['code', 'creator', 'created_at', 'updated_at', 'updater'];

    public function matter() {
        return $this->hasMany('App\Matter');
    }
}
