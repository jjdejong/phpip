<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

class Rule extends Model
{
    use HasTableComments;
    
    protected $table = 'task_rules';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function country()
    {
        return $this->belongsTo(Country::class, 'for_country', 'iso');
    }

    public function origin()
    {
        return $this->belongsTo(Country::class, 'for_origin', 'iso');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'for_category', 'code');
    }

    public function trigger()
    {
        return $this->belongsTo(EventName::class, 'trigger_event');
    }

    public function taskInfo()
    {
        return $this->belongsTo(EventName::class, 'task');
    }

    public function type()
    {
        return $this->belongsTo(MatterType::class, 'for_type', 'code');
    }

    public function condition_eventInfo()
    {
        return $this->belongsTo(EventName::class, 'condition_event');
    }

    public function abort_onInfo()
    {
        return $this->belongsTo(EventName::class, 'abort_on');
    }

    public function responsibleInfo()
    {
        return $this->belongsTo(Actor::class, 'responsible', 'login');
    }

    public function templates()
    {
        return $this->belongsToMany(TemplateClass::class, 'rule_class_lnk', 'task_rule_id', 'template_class_id');
    }
}
