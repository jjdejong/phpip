<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslations;
use App\Models\Translations\ClassifierTypeTranslation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ClassifierType extends Model
{
    use HasTableComments, HasTranslations;
    
    protected $table = 'classifier_type';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];
    
    /**
     * Get the translated type attribute.
     */
    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $this->getTranslation('type'),
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
     * Get the translations for this classifier type.
     */
    public function translations()
    {
        return $this->hasMany(ClassifierTypeTranslation::class, 'code', 'code');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'for_category', 'code');
    }
}
