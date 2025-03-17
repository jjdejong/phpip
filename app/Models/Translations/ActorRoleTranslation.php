<?php

namespace App\Models\Translations;

use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class ActorRoleTranslation extends Model
{
    protected $table = 'actor_role_translations';
    
    protected $fillable = [
        'code',
        'locale',
        'name',
        'notes'
    ];
    
    public function role()
    {
        return $this->belongsTo(Role::class, 'code', 'code');
    }
}