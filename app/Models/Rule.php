<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslations;
use App\Models\Translations\TaskRuleTranslation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Rule extends Model
{
    use HasTableComments, HasTranslations;
    
    protected $table = 'task_rules';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    /**
     * Get the translated detail attribute.
     */
    protected function detail(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->getTranslation('detail'),
        );
    }
    
    /**
     * Get the translated notes attribute.
     */
    protected function notes(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->getTranslation('notes'),
        );
    }
    
    /**
     * Get the translations for this rule.
     */
    public function translations()
    {
        return $this->hasMany(TaskRuleTranslation::class, 'task_rule_id', 'id');
    }

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
        return $this->belongsTo(Type::class, 'for_type', 'code');
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
