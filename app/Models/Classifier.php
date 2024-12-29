<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classifier extends Model
{
    protected $table = 'classifier';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $touches = ['matter'];

    public function type()
    {
        return $this->belongsTo(ClassifierType::class, 'type_code');
    }

    public function linkedMatter()
    {
        return $this->belongsTo(Matter::class, 'lnk_matter_id');
    }

    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }
}
