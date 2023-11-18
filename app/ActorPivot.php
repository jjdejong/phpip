<?php

namespace App;

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

    // use \Venturecraft\Revisionable\RevisionableTrait;
    // protected $revisionEnabled = true;
    // protected $revisionCreationsEnabled = true;
    // protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    // protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

    public function matter()
    {
        return $this->belongsTo(\App\Matter::class);
    }

    public function actor()
    {
        return $this->belongsTo(\App\Actor::class);
    }

    public function role()
    {
        return $this->belongsTo(\App\Role::class, 'role');
    }

    public function company()
    {
        return $this->belongsTo(\App\Actor::class, 'company_id');
    }
}
