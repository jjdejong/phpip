<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslationsExtended;

/**
 * EventName Model
 *
 * Represents event and task types used throughout the system, including:
 * - Filing dates (FIL)
 * - Publication dates (PUB)
 * - Grant dates (GRT)
 * - Renewal tasks (REN)
 * - Status events and custom deadlines
 *
 * Database table: event_name
 *
 * Key relationships:
 * - Has many events of this type
 * - Has many tasks of this type
 * - Can belong to a country (country-specific events)
 * - Can belong to a category (category-specific events)
 * - Has many associated document templates
 *
 * Business logic:
 * - Event names define what types of events and tasks can be created
 * - Some event names are marked as status events (show in matter status)
 * - Event names can have default responsible parties
 * - Event names are translatable for multi-language support
 * - Uses string code as primary key
 * - Category and country fields make event names context-specific
 */
class EventName extends Model
{
    use HasTableComments;
    use HasTranslationsExtended;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'event_name';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Attributes that should be hidden from serialization.
     *
     * @var array<string>
     */
    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Attributes that support multi-language translations.
     *
     * @var array<string>
     */
    public $translatable = ['name'];

    /**
     * Get all events of this type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function events()
    {
        return $this->hasMany(Event::class, 'code');
    }

    /**
     * Get all tasks of this type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'code');
    }

    /**
     * Get the country this event name is specific to.
     *
     * Null means applies to all countries.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function countryInfo()
    {
        return $this->belongsTo(Country::class, 'country', 'iso');
    }

    /**
     * Get the category this event name is specific to.
     *
     * Null means applies to all categories.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoryInfo()
    {
        return $this->belongsTo(Category::class, 'category', 'code');
    }

    /**
     * Get the default responsible user for events/tasks of this type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function default_responsibleInfo()
    {
        return $this->belongsTo(User::class, 'default_responsible', 'login');
    }

    /**
     * Get the document templates associated with this event name.
     *
     * Templates can be used to generate documents for events/tasks of this type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function templates()
    {
        return $this->belongsToMany(TemplateClass::class, 'event_class_lnk', 'event_name_code', 'template_class_id');
    }
}
