<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateClass extends Model
{
  protected $guarded = ['created_at', 'updated_at'];
  
    public function category() {
      return $this->belongsTo('App\TemplateCategory');
    }

    public function role() {
      return $this->belongsTo('App\Role', 'default_role', 'code');
    }
}
