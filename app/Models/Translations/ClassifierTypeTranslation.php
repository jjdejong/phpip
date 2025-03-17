<?php

namespace App\Models\Translations;

use App\Models\ClassifierType;
use Illuminate\Database\Eloquent\Model;

class ClassifierTypeTranslation extends Model
{
    protected $table = 'classifier_type_translations';
    
    protected $fillable = [
        'code',
        'locale',
        'type',
        'notes'
    ];
    
    public function classifierType()
    {
        return $this->belongsTo(ClassifierType::class, 'code', 'code');
    }
}