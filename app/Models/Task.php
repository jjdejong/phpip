<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasTranslationsExtended;

/**
 * Task Model
 *
 * Represents reminders, deadlines, and renewals automatically generated from events
 * based on rules. Tasks are the primary mechanism for deadline management in phpIP.
 *
 * Database table: task
 *
 * Key relationships:
 * - Belongs to an event (trigger) that generated the task
 * - Belongs to a rule that defined the task generation logic
 * - Has access to matter through the trigger event
 * - Has event name info describing the task type
 *
 * Business logic:
 * - Tasks are automatically created by rules when events occur
 * - Tasks can be assigned to specific users or inherit matter responsibility
 * - Renewal tasks (REN) have special handling for fee calculations
 * - Tasks automatically touch (update timestamp of) their parent matter
 * - Task details are translatable (multi-language support)
 * - Open tasks exclude those from dead matters
 */
class Task extends Model
{
    use HasTranslationsExtended;

    /**
     * The database table associated with the model.
     *
     * @var string
     */
    protected $table = 'task';

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
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Related models that should be touched when this model is updated.
     *
     * Updates the matter's timestamp when a task changes.
     *
     * @var array<string>
     */
    protected $touches = ['matter'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'due_date' => 'date:Y-m-d',
        'done_date' => 'date:Y-m-d',
    ];

    /**
     * Attributes that support multi-language translations.
     *
     * @var array<string>
     */
    public $translatable = ['detail'];

    /**
     * Get the event name information for this task.
     *
     * Returns the EventName model containing description and classification.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function info()
    {
        return $this->belongsTo(EventName::class, 'code');
    }

    /**
     * Get the event that triggered (generated) this task.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function trigger()
    {
        return $this->belongsTo(Event::class, 'trigger_id');
    }

    /**
     * Get the matter associated with this task.
     *
     * Uses a has-one-through relationship via the trigger event.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function matter()
    {
        return $this->hasOneThrough(Matter::class, Event::class, 'id', 'id', 'trigger_id', 'matter_id');
    }

    /**
     * Get the rule that was used to generate this task.
     *
     * Rules define the logic for automatic task creation from events.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rule(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Rule::class, 'rule_used', 'id');
    }

    /**
     * Get open task counts grouped by user.
     *
     * Returns a summary of undone tasks for each user/responsible, including:
     * - Number of open tasks per user
     * - Most urgent (earliest) due date per user
     * - User login/identifier
     *
     * Respects user role restrictions:
     * - Clients see only their own matters' tasks
     * - Can filter by assigned user (what_tasks=1) or client (what_tasks>1)
     * - Excludes tasks from dead matters
     *
     * @return \Illuminate\Support\Collection Collection of task counts by user
     */
    public static function getUsersOpenTaskCount()
    {
        $userid = Auth::user()->id;
        $role = Auth::user()->default_role;
        $what_tasks = request()->input('what_tasks');

        $query = static::with(['matter', 'matter.client'])
            ->where('done', 0)
            ->whereHas('matter', function (Builder $q) {
                $q->where('dead', 0);
            });

        // Apply filters based on what_tasks parameter
        if ($what_tasks == 1) {
            // My tasks - filter by assigned_to
            $query->where('assigned_to', Auth::user()->login);
        } elseif ($what_tasks > 1) {
            // Client tasks - filter by client ID
            $query->whereHas('matter.client', function ($q) use ($what_tasks) {
                $q->where('actor_id', $what_tasks);
            });
        }

        // Apply client role restrictions if needed
        if ($role == 'CLI' || empty($role)) {
            $query->whereHas('matter', function ($q) use ($userid) {
                $q->whereHas('client', function ($q2) use ($userid) {
                    $q2->where('actor_id', $userid);
                });
            });
        }

        // Select and group results
        return $query->select(
                DB::raw('count(*) as no_of_tasks'),
                DB::raw('MIN(due_date) as urgent_date'),
                DB::raw('IFNULL(assigned_to, (SELECT responsible FROM matter WHERE id = (SELECT matter_id FROM event WHERE id = task.trigger_id))) as login')
            )
            ->groupBy('login')
            ->get();
    }

    /**
     * Scope query to open tasks.
     *
     * Returns tasks that are not done and belong to matters that are not dead.
     * Eager loads event info, matter titles, and client for efficient querying.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function openTasks()
    {
        return $this->with(['info', 'matter.titles', 'matter.client'])
            ->where('done', 0)
            ->whereHas('matter', function (Builder $q) {
                $q->where('dead', 0);
            });
    }

    /**
     * Build a comprehensive query for renewal tasks with fees and matter details.
     *
     * This complex query joins tasks with their matters, events, actors, and fees to provide
     * all information needed for renewal management and invoicing. Includes:
     * - Task and matter details
     * - Applicant/owner information with small entity status
     * - Client information for billing
     * - Fee calculations with various discount scenarios
     * - Filing, grant, and publication dates
     * - Multi-language country names and titles
     *
     * Used primarily for renewal reports and fee estimation.
     *
     * @return \Illuminate\Database\Eloquent\Builder Query builder for renewal tasks
     */
    public static function renewals()
    {
        // The query is complex but optimized for performance by using joins and raw SQL for some calculations and conditions.
        return Matter::select([
            'task.id',
            DB::raw("JSON_UNQUOTE(JSON_EXTRACT(task.detail, '$.\"en\"')) AS detail"),
            'task.due_date',
            'task.done',
            'task.done_date',
            'event.matter_id',
            DB::raw('IFNULL(fees.cost, task.cost) AS cost'),
            DB::raw('IFNULL(fees.fee, task.fee) AS fee'),
            DB::raw('COALESCE(fees.cost_reduced, fees.cost, task.cost) AS cost_reduced'),
            DB::raw('COALESCE(fees.fee_reduced, fees.fee, task.fee) AS fee_reduced'),
            DB::raw('COALESCE(fees.cost_sup, fees.cost, task.cost) AS cost_sup'),
            DB::raw('COALESCE(fees.fee_sup, fees.fee, task.fee) AS fee_sup'),
            DB::raw('COALESCE(fees.cost_sup_reduced, fees.cost, task.cost) AS cost_sup_reduced'),
            DB::raw('COALESCE(fees.fee_sup_reduced, fees.fee, task.fee) AS fee_sup_reduced'),
            'task.trigger_id',
            'matter.category_code AS category',
            'matter.caseref',
            'matter.uid',
            'matter.country',
            DB::raw('JSON_UNQUOTE(JSON_EXTRACT(mcountry.name, "$.fr")) AS country_FR'),
            DB::raw('JSON_UNQUOTE(JSON_EXTRACT(mcountry.name, "$.en")) AS country_EN'),
            DB::raw('JSON_UNQUOTE(JSON_EXTRACT(mcountry.name, "$.de")) AS country_DE'),
            'matter.origin',
            DB::raw('COALESCE(MIN(own.small_entity), MIN(ownc.small_entity), MIN(appl.small_entity), MIN(applc.small_entity)) AS small_entity'),
            'fil.event_date AS fil_date',
            'fil.detail AS fil_num',
            'grt.event_date AS grt_date',
            'event.code AS event_name',
            'event.event_date',
            'event.detail AS number',
            DB::raw("IF(GROUP_CONCAT(DISTINCT ownc.name) IS NOT NULL OR GROUP_CONCAT(DISTINCT own.name) IS NOT NULL,
                CONCAT_WS('; ', GROUP_CONCAT(DISTINCT ownc.name SEPARATOR '; '), GROUP_CONCAT(DISTINCT own.name SEPARATOR '; ')),
                CONCAT_WS('; ', GROUP_CONCAT(DISTINCT applc.name SEPARATOR '; '), GROUP_CONCAT(DISTINCT appl.name SEPARATOR '; '))
            ) AS applicant_name"),
            DB::raw('COALESCE(pa_cli.name, clic.name) AS client_name'),
            DB::raw('COALESCE(pa_cli.address, clic.address) AS client_address'),
            DB::raw('COALESCE(pa_cli.country, clic.country) AS client_country'),
            DB::raw('COALESCE(pa_cli.ren_discount, clic.ren_discount) AS discount'),
            DB::raw('COALESCE(pmal_cli.actor_id, cliclnk.actor_id) AS client_id'),
            DB::raw('COALESCE(pmal_cli.actor_ref, cliclnk.actor_ref) AS client_ref'),
            DB::raw('COALESCE(pa_cli.email, clic.email) AS email'),
            DB::raw('COALESCE(pa_cli.language, clic.language) AS language'),
            'matter.responsible',
            'tit.value AS short_title',
            'titof.value AS title',
            'pub.detail AS pub_num',
            'task.step',
            'task.grace_period',
            'task.invoice_step',
            'matter.expire_date',
            'fees.fee AS table_fee'
        ])
        ->join('event', 'matter.id', 'event.matter_id')
        ->join('task', 'task.trigger_id', 'event.id')
        ->leftJoin('country as mcountry', 'mcountry.iso', 'matter.country')
        // Events
        ->leftJoin('event AS fil', fn($join) => 
            $join->on('matter.id', 'fil.matter_id')
                 ->where('fil.code', 'FIL'))
        ->leftJoin('event AS pub', fn($join) => 
            $join->on('matter.id', 'pub.matter_id')
                 ->where('pub.code', 'PUB'))
        ->leftJoin('event AS grt', fn($join) => 
            $join->on('matter.id', 'grt.matter_id')
                 ->where('grt.code', 'GRT'))
        // Applicants and owners
        ->leftJoin(DB::raw("matter_actor_lnk lappl JOIN actor appl ON appl.id = lappl.actor_id AND lappl.role = 'APP'"),
            'matter.id', 'lappl.matter_id')
        ->leftJoin(DB::raw("matter_actor_lnk lapplc JOIN actor applc ON applc.id = lapplc.actor_id AND lapplc.role = 'APP' AND lapplc.shared = 1"),
            'matter.container_id', 'lapplc.matter_id')
        ->leftJoin(DB::raw("matter_actor_lnk lown JOIN actor own ON own.id = lown.actor_id AND lown.role = 'OWN'"),
            'matter.id', 'lown.matter_id')
        ->leftJoin(DB::raw("matter_actor_lnk lownc JOIN actor ownc ON ownc.id = lownc.actor_id AND lownc.role = 'OWN' AND lownc.shared = 1"),
            'matter.container_id', 'lownc.matter_id')
        // Clients
        ->leftJoin(DB::raw('matter_actor_lnk pmal_cli JOIN actor pa_cli ON pa_cli.id = pmal_cli.actor_id'), 
            fn($join) => $join->on('matter.id', 'pmal_cli.matter_id')->where('pmal_cli.role', 'CLI'))
        ->leftJoin(DB::raw('matter_actor_lnk cliclnk JOIN actor clic ON clic.id = cliclnk.actor_id'),
            fn($join) => $join->on('matter.container_id', 'cliclnk.matter_id')
                             ->where([['cliclnk.role', 'CLI'], ['cliclnk.shared', 1]]))
        // Titles
        ->leftJoin('classifier AS tit', fn($join) => 
            $join->on(DB::raw('IFNULL(matter.container_id, matter.id)'), 'tit.matter_id')
                 ->where('tit.type_code', 'TIT'))
        ->leftJoin('classifier AS titof', fn($join) => 
            $join->on(DB::raw('IFNULL(matter.container_id, matter.id)'), 'titof.matter_id')
                 ->where('titof.type_code', 'TITOF'))
        // Fees
        ->leftJoin('fees', function($join) {
            $join->on('fees.for_country', 'matter.country')
                ->on('fees.for_category', 'matter.category_code')
                ->on(DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(task.detail, '$.\"en\"')) AS UNSIGNED)"), 'fees.qt');
        })
        ->where('task.code', 'REN')
        ->groupBy('task.due_date')
        ->groupBy('task.id')
        ->groupBy('event.matter_id');
    }
}
