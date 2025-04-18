<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use Spatie\Translatable\HasTranslations;

class ClassifierType extends Model
{
    use HasTableComments;
    use HasTranslations;
    
    protected $table = 'classifier_type';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];

    public $translatable = ['type'];
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'for_category', 'code');
    }
}
