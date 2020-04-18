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

    public function rules() {
        return $this->belongsToMany('App\Rule','rule_class_lnk','template_class_id','task_rule_id');
    }

    public function eventNames() {
        return $this->belongsToMany('App\EventName','event_class_lnk','template_class_id','event_name_code');
    }
}
