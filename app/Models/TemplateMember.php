<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

class TemplateMember extends Model
{
    use HasTableComments;
    
    protected $guarded = ['created_at', 'updated_at'];

    public function class()
    {
        return $this->belongsTo(TemplateClass::class);
    }
}
