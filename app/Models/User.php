<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model
 *
 * Represents authenticated users of the phpIP system. Users are typically internal
 * staff members who manage IP matters, though the system also supports client users
 * with restricted access.
 *
 * Database table: users
 *
 * Key relationships:
 * - Belongs to a role (determines permissions)
 * - Belongs to a company (organizational hierarchy)
 * - Belongs to a parent user (supervisor relationship)
 * - Has many matters as responsible party
 *
 * Business logic:
 * - Extends Laravel's Authenticatable for login/auth functionality
 * - Users have a default_role that determines their system permissions
 * - Client users see only their own matters
 * - Internal users can be assigned as responsible for matters
 * - Users inherit Actor fields for contact information
 * - Passwords are automatically hashed
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Attributes that should be hidden from serialization.
     *
     * Includes sensitive authentication data.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the role information for this user.
     *
     * The role determines permissions and UI behavior.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function roleInfo()
    {
        return $this->belongsTo(Role::class, 'default_role');
    }

    /**
     * Get the company this user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Actor::class, 'company_id');
    }

    /**
     * Get the parent user (supervisor) for this user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Actor::class, 'parent_id');
    }

    /**
     * Get all matters this user is responsible for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function matters()
    {
        return $this->hasMany(Matter::class, 'responsible', 'login');
    }

    /**
     * Get matters with pending tasks for this user.
     *
     * Returns matters where this user is responsible and has undone tasks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->matters()->has('tasksPending')->with('tasksPending');
    }

    /**
     * Get matters with pending renewals for this user.
     *
     * Returns matters where this user is responsible and has undone renewal tasks.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function renewals()
    {
        return $this->matters()->has('renewalsPending')->with('renewalsPending');
    }
}
