<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Rule extends Model
{
    protected $table = 'task_rules';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function country()
    {
        return $this->belongsTo(\App\Country::class, 'for_country', 'iso');
    }

    public function origin()
    {
        return $this->belongsTo(\App\Country::class, 'for_origin', 'iso');
    }

    public function category()
    {
        return $this->belongsTo(\App\Category::class, 'for_category', 'code');
    }

    public function trigger()
    {
        return $this->belongsTo(\App\EventName::class, 'trigger_event');
    }

    public function taskInfo()
    {
        return $this->belongsTo(\App\EventName::class, 'task');
    }

    public function type()
    {
        return $this->belongsTo(\App\Type::class, 'for_type', 'code');
    }

    public function condition_eventInfo()
    {
        return $this->belongsTo(\App\EventName::class, 'condition_event');
    }

    public function abort_onInfo()
    {
        return $this->belongsTo(\App\EventName::class, 'abort_on');
    }

    public function responsibleInfo()
    {
        return $this->belongsTo(\App\Actor::class, 'responsible', 'login');
    }

    public function templates()
    {
        return $this->belongsToMany(\App\TemplateClass::class, 'rule_class_lnk', 'task_rule_id', 'template_class_id');
    }

    public function getTableComments($table_name = null)
    {
        if (! isset($table_name)) {
            return false;
        }

        $comments = [];
        foreach (Schema::getColumns($table_name) as $column) {
            $col_name = $column['name'];
            $comments[$col_name] = $column['comment'];
        }

        return $comments;
    }
}
