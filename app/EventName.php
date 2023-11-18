<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventName extends Model
{
    protected $table = 'event_name';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];

    public function events()
    {
        return $this->hasMany(\App\Event::class, 'code');
    }

    public function tasks()
    {
        return $this->hasMany(\App\Task::class, 'code');
    }

    public function countryInfo()
    {
        return $this->belongsTo(\App\Country::class, 'country', 'iso');
    }

    public function categoryInfo()
    {
        return $this->belongsTo(\App\Category::class, 'category', 'code');
    }

    public function default_responsibleInfo()
    {
        return $this->belongsTo(\App\User::class, 'default_responsible', 'login');
    }

    public function templates()
    {
        return $this->belongsToMany(\App\TemplateClass::class, 'event_class_lnk', 'event_name_code', 'template_class_id');
    }
}
