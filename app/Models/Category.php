<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslations;
use App\Models\Translations\MatterCategoryTranslation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Category extends Model
{
    use HasTableComments, HasTranslations;
    
    protected $table = 'matter_category';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];
    
    /**
     * Get the translated category attribute.
     */
    protected function category(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $this->getTranslation('category'),
        );
    }
    
    /**
     * Get the translations for this category.
     */
    public function translations()
    {
        return $this->hasMany(MatterCategoryTranslation::class, 'code', 'code');
    }

    public function matter()
    {
        return $this->hasMany(Matter::class);
    }

    public function displayWithInfo()
    {
        return $this->belongsTo(Category::class, 'display_with', 'code');
    }
}
