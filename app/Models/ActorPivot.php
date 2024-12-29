<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ActorPivot extends Pivot
{
    protected $table = 'matter_actor_lnk';

    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $touches = ['matter'];
    /*protected $casts = [
        'date' => 'date:Y-m-d'
    ];*/

    public function matter()
    {
        return $this->belongsTo(Matter::class);
    }

    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role');
    }

    public function company()
    {
        return $this->belongsTo(Actor::class, 'company_id');
    }
}
