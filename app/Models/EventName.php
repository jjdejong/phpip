<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslations;
use App\Models\Translations\EventNameTranslation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EventName extends Model
{
    use HasTableComments, HasTranslations;
    
    protected $table = 'event_name';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];
    
    /**
     * Get the translated name attribute.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                $translated = $this->getTranslation('name');
                \Log::debug('EventName name accessor: locale=' . app()->getLocale() . ', raw=' . $this->getRawOriginal('name') . ', translated=' . $translated);
                return $translated;
            },
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
     * Get the translations for this event name.
     */
    public function translations()
    {
        return $this->hasMany(EventNameTranslation::class, 'code', 'code');
    }

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
