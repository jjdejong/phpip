<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventClassLnk extends Model
{
    protected $table = 'event_class_lnk';

    protected $guarded = [];

    public function class()
    {
        return $this->belongsTo(\App\Models\TemplateClass::class, 'template_class_id');
    }
}
