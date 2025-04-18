<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use Spatie\Translatable\HasTranslations;

class Role extends Model
{
    use HasTableComments;
    use HasTranslations;
    
    protected $table = 'actor_role';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];

    public $incrementing = false;

    // Define which attributes are translatable
    public $translatable = ['name'];
}
