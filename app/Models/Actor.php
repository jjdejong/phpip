<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

class Actor extends Model
{
    use HasTableComments;
    
    protected $table = 'actor';

    protected $hidden = ['login', 'last_login', 'password', 'remember_token', 'creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['id', 'password', 'created_at', 'updated_at'];

    public function company()
    {
        return $this->belongsTo(Actor::class, 'company_id');
    }

    public function parent()
    {
        return $this->belongsTo(Actor::class, 'parent_id');
    }

    public function site()
    {
        return $this->belongsTo(Actor::class, 'site_id');
    }

    public function matters()
    {
        return $this->belongsToMany(Matter::class, 'matter_actor_lnk');
    }

    public function mattersWithLnk()
    {
        return $this->hasMany(ActorPivot::class, 'actor_id');
    }

    public function droleInfo()
    {
        return $this->belongsTo(Role::class, 'default_role');
    }

    public function countryInfo()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    public function country_mailingInfo()
    {
        return $this->belongsTo(Country::class, 'country_mailing');
    }

    public function country_billingInfo()
    {
        return $this->belongsTo(Country::class, 'country_billing');
    }

    public function nationalityInfo()
    {
        return $this->belongsTo(Country::class, 'nationality');
    }
}
