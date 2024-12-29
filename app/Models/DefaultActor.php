<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;

class DefaultActor extends Model
{
    use HasTableComments;
    
    protected $table = 'default_actor';

    protected $guarded = ['created_at', 'updated_at'];

    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'for_country', 'iso');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'for_category', 'code');
    }

    public function client()
    {
        return $this->belongsTo(Actor::class, 'for_client');
    }

    public function roleInfo()
    {
        return $this->belongsTo(Role::class, 'role', 'code');
    }
}
