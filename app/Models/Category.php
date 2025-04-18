<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTableComments;
    use HasTranslations;
    
    protected $table = 'matter_category';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];

    public $translatable = ['category'];

    public function matters()
    {
        return $this->hasMany(Matter::class, 'category_code', 'code');
    }

    public function displayWithInfo()
    {
        return $this->belongsTo(Category::class, 'display_with', 'code');
    }
}
