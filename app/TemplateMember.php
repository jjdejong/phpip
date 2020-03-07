<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateMember extends Model
{
    public function style() {
      return $this->belongsTo('App\TemplateStyle');
    }

    public function category() {
      return $this->belongsTo('App\TemplateCategory');
    }

    public function language() {
      return $this->belongsTo('App\Language');
    }

    public function class() {
      return $this->belongsTo('App\TemplateClass');
    }
}
