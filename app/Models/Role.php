<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTableComments;
use App\Traits\HasTranslationsExtended;

/**
 * Role Model
 *
 * Represents roles that actors can have in matters and in the system, including:
 * - Matter roles: CLI (Client), AGT (Agent), INV (Inventor), APP (Applicant), etc.
 * - System roles: Administrator, User, Client (for access control)
 *
 * Database table: actor_role
 *
 * Key relationships:
 * - Used in matter-actor relationships to define actor function
 * - Used as default role for actors (determines permissions)
 *
 * Business logic:
 * - Roles define both functional relationships and access permissions
 * - Some roles are system roles (affect UI and authorization)
 * - Some roles are matter-specific (define actor's function in a matter)
 * - Role names are translatable for multi-language support
 * - Uses string code as primary key
 */
class Role extends Model
{
    use HasTableComments;
    use HasTranslationsExtended;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'actor_role';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'code';

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Attributes that should be hidden from serialization.
     *
     * @var array<string>
     */
    protected $hidden = ['creator', 'created_at', 'updated_at', 'updater'];

    /**
     * Attributes that are not mass assignable.
     *
     * @var array<string>
     */
    protected $guarded = ['created_at', 'updated_at'];

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Attributes that support multi-language translations.
     *
     * @var array<string>
     */
    public $translatable = ['name'];
}
