<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

class TemplateClass extends Model
{
    use HasTableComments;
    
    protected $guarded = ['created_at', 'updated_at'];

    public function role()
    {
        return $this->belongsTo(Role::class, 'default_role', 'code');
    }

    public function rules()
    {
        return $this->belongsToMany(Rule::class, 'rule_class_lnk', 'template_class_id', 'task_rule_id');
    }

    public function eventNames()
    {
        return $this->belongsToMany(EventName::class, 'event_class_lnk', 'template_class_id', 'event_name_code');
    }
}
