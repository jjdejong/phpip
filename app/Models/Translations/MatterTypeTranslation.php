<?php

namespace App\Models\Translations;

use App\Models\MatterType;
use Illuminate\Database\Eloquent\Model;

class MatterTypeTranslation extends Model
{
    protected $table = 'matter_type_translations';
    
    protected $fillable = [
        'code',
        'locale',
        'type'
    ];
    
    public function matterType()
    {
        return $this->belongsTo(MatterType::class, 'code', 'code');
    }
}