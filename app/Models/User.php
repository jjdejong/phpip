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
     * The roles that make a login a member of staff. Everything else is a client.
     */
    public const STAFF_ROLES = ['DBA', 'DBRW', 'DBRO'];

    /**
     * Memoized result of clientActorIds().
     */
    private ?array $clientActorIds = null;

    /**
     * Whether this user is staff: one of the database roles, which see everything.
     */
    public function isStaff(): bool
    {
        return in_array($this->default_role, self::STAFF_ROLES, true);
    }

    /**
     * Whether this user is a client user (restricted access).
     *
     * Defined as "not staff" rather than "role is CLI", because default_role
     * does two jobs: for a login it is the permission set, but for any actor it
     * is also the role proposed when linking them to a matter. A client contact
     * is naturally CNT, and forcing them to CLI to grant portal access breaks
     * that. With this definition any login outside the staff roles - CLI, CNT,
     * blank, or anything else - is restricted, so a new role can never
     * accidentally grant access to every matter.
     */
    public function isClient(): bool
    {
        return ! $this->isStaff();
    }

    /**
     * The client company whose matters this user may see, or null for none.
     *
     * Matters are normally linked to the client *company* actor, so a contact
     * has to be able to see their company's matters. The company is read from
     * actor.company_id. A client that IS a company carries no company_id of its
     * own, so it acts as its own company and thereby also sees the matters of
     * its contacts - the scope has to work in both directions, or two logins of
     * the same client disagree about what they can see.
     *
     * Returns null for staff, and for a login whose default_role is empty: a
     * blank role is an unconfigured account, not a grant, so it keeps only its
     * own matters. Any other client login - CLI for a company, CNT for one of
     * its contacts - gets the company scope.
     *
     * Note this makes actor.company_id an authorization input. Every actor
     * employed by the company is treated as an identity of that client, so an
     * employee who is separately the client of record on a private matter would
     * expose it to their colleagues. Curating company_id on client logins is
     * therefore a security task, not just bookkeeping.
     */
    public function clientCompanyId(): ?int
    {
        if ($this->isStaff() || empty($this->default_role)) {
            return null;
        }

        return (int) ($this->company_id ?: $this->id);
    }

    /**
     * The actors whose client links this user may see: the user, and when it has
     * a client company, that company and every actor employed by it.
     *
     * Resolved once per request, as it is used for every scoped query.
     */
    public function clientActorIds(): array
    {
        if ($this->clientActorIds === null) {
            $ids = [$this->id];
            if ($companyId = $this->clientCompanyId()) {
                $ids[] = $companyId;
                array_push($ids, ...Actor::where('company_id', $companyId)->pluck('id')->all());
            }
            $this->clientActorIds = array_values(array_unique($ids));
        }

        return $this->clientActorIds;
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
