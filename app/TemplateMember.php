<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateMember extends Model
{
  protected $guarded = ['created_at', 'updated_at'];

    public function style() {
      return $this->belongsTo('App\TemplateStyle');
    }

    public function language() {
      return $this->belongsTo('App\Language');
    }

    public function class() {
      return $this->belongsTo('App\TemplateClass');
    }
}
