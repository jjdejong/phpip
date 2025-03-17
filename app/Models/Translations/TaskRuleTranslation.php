<?php

namespace App\Models\Translations;

use App\Models\Rule;
use Illuminate\Database\Eloquent\Model;

class TaskRuleTranslation extends Model
{
    protected $table = 'task_rules_translations';
    
    protected $fillable = [
        'task_rule_id',
        'locale',
        'detail',
        'notes'
    ];
    
    public function rule()
    {
        return $this->belongsTo(Rule::class, 'task_rule_id', 'id');
    }
}