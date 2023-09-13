<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventClassLnk extends Model
{
    protected $table = 'event_class_lnk';
    protected $guarded = [];

    public function class() {
      return $this->belongsTo(\App\TemplateClass::class,'template_class_id');
    }
}
