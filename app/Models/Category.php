<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

class Category extends Model
{
    use HasTableComments;
    
    protected $table = 'matter_category';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];

    public function matter()
    {
        return $this->hasMany(Matter::class);
    }

    public function displayWithInfo()
    {
        return $this->belongsTo(Category::class, 'display_with', 'code');
    }
}