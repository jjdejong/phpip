<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslations;
use App\Models\Translations\ActorRoleTranslation;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Role extends Model
{
    use HasTableComments, HasTranslations;
    
    protected $table = 'actor_role';

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['created_at', 'updated_at'];

    public $incrementing = false;
    
    /**
     * Get the translated name attribute.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $this->getTranslation('name'),
        );
    }
    
    /**
     * Get the translated notes attribute.
     */
    protected function notes(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $this->getTranslation('notes'),
        );
    }
    
    /**
     * Get the translations for this role.
     */
    public function translations()
    {
        return $this->hasMany(ActorRoleTranslation::class, 'code', 'code');
    }
}
