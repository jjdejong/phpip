<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassifierType extends Model
{
    protected $table = 'classifier_type';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];

    public function category()
    {
        return $this->belongsTo(\App\Category::class, 'for_category', 'code');
    }
}
