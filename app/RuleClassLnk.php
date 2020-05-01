<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RuleClassLnk extends Model
{
    protected $table = 'rule_class_lnk';
    protected $guarded = [];
    
    public function class() {
      return $this->belongsTo('App\TemplateClass','template_class_id');
    }
}
