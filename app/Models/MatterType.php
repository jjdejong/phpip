<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslations;
use App\Models\Translations\MatterTypeTranslation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class MatterType extends Model
{
    use HasTableComments, HasTranslations;
    
    protected $table = 'matter_type';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

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
     * Get the translations for this matter type.
     */
    public function translations()
    {
        return $this->hasMany(MatterTypeTranslation::class, 'code', 'code');
    }
}
