<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateMember extends Model
{
    protected $guarded = ['created_at', 'updated_at'];

    public function class()
    {
        return $this->belongsTo(\App\Models\TemplateClass::class);
    }
}
