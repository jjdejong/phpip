<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class ActorPivot
 *
 * Represents the pivot table for the many-to-many relationship between Matter and Actor models.
 */
class ActorPivot extends Pivot
{
    /**
     * @var string The table associated with the pivot model.
     */
    protected $table = 'matter_actor_lnk';

    /**
     * @var array The attributes that should be hidden for arrays.
     */
    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    /**
     * @var array The attributes that aren't mass assignable.
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var array The relationships that should be touched on save.
     */
    protected $touches = ['matter'];

    /**
     * Get the matter that owns the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matter(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Matter::class, 'matter_id');
    }

    /**
     * Get the actor that owns the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Actor::class, 'actor_id');
    }

    /**
     * Get the role associated with the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class, 'role');
    }

    /**
     * Get the company associated with the pivot.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Actor::class, 'company_id');
    }
}