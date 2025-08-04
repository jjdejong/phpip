<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslationsExtended;

class Country extends Model
{
    use HasTranslationsExtended;

    protected $table = 'country';

    protected $primaryKey = 'iso';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $hidden = ['iso3', 'numcode'];

    // Define which attributes are translatable
    public $translatable = ['name'];

    protected $guarded = [];

    public function getGoesnationalAttribute() // Defines "goesnational" as an attribute
    {
        return in_array($this->iso, ['EP', 'WO', 'EM', 'OA']);
    }

    public function getNatcountriesAttribute()
    {
        if ($this->goesnational) {
            return $this->where("$this->iso", 1)->pluck('name', 'iso');
        } else {
            return null;
        }
    }
}
