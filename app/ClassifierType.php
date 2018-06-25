<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassifierType extends Model {

    protected $table = 'classifier_type';
    protected $primaryKey = 'code';
    public $incrementing = false;
    public $timestamps = false;
    protected $hidden = ['creator', 'updated', 'updater'];
    protected $guarded = ['code', 'creator', 'updated', 'updater'];

}
