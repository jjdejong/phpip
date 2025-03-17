<?php

namespace App\Models\Translations;

use App\Models\EventName;
use Illuminate\Database\Eloquent\Model;

class EventNameTranslation extends Model
{
    protected $table = 'event_name_translations';
    
    protected $fillable = [
        'code',
        'locale',
        'name',
        'notes'
    ];
    
    public function eventName()
    {
        return $this->belongsTo(EventName::class, 'code', 'code');
    }
}