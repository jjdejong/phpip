<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateClass extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class, 'default_role', 'code');
    }

    public function rules()
    {
        return $this->belongsToMany(\App\Models\Rule::class, 'rule_class_lnk', 'template_class_id', 'task_rule_id');
    }

    public function eventNames()
    {
        return $this->belongsToMany(\App\Models\EventName::class, 'event_class_lnk', 'template_class_id', 'event_name_code');
    }
}
