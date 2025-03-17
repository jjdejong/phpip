<?php

namespace App\Models\Translations;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class MatterCategoryTranslation extends Model
{
    protected $table = 'matter_category_translations';
    
    protected $fillable = [
        'code',
        'locale',
        'category'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'code', 'code');
    }
}