<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslationsExtended;

class EventName extends Model
{
    use HasTableComments;
    use HasTranslationsExtended;

    protected $table = 'event_name';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];

    // Define which attributes are translatable
    public $translatable = ['name'];

    public function events()
    {
        return $this->hasMany(Event::class, 'code');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'code');
    }

    public function countryInfo()
    {
        return $this->belongsTo(Country::class, 'country', 'iso');
    }

    public function categoryInfo()
    {
        return $this->belongsTo(Category::class, 'category', 'code');
    }

    public function default_responsibleInfo()
    {
        return $this->belongsTo(User::class, 'default_responsible', 'login');
    }

    public function templates()
    {
        return $this->belongsToMany(TemplateClass::class, 'event_class_lnk', 'event_name_code', 'template_class_id');
    }
}
