<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Renewal extends Model
{
    protected $table = 'renewal_list';
    protected $guarded = ['id'];

    public static function list()
    {
        $query = Matter::select(
            'task.id AS id',
            'task.detail AS detail',
            'task.due_date AS due_date',
            'task.done AS done',
            'task.done_date AS done_date',
            'event.matter_id AS matter_id',
            'fees.cost AS cost',
            'fees.fee AS fee',
            'fees.cost_reduced AS cost_reduced',
            'fees.fee_reduced AS fee_reduced',
            'fees.cost_sup AS cost_sup',
            'fees.fee_sup AS fee_sup',
            'fees.fee_sup_reduced AS fee_sup_reduced',
            'fees.cost_sup_reduced AS cost_sup_reduced',
            'task.trigger_id AS trigger_id',
            'matter.category_code AS category',
            'matter.caseref AS caseref',
            'matter.suffix AS suffix',
            'matter.country AS country',
            'mcountry.name_FR AS country_FR',
            'matter.origin AS origin',
            'matter.type_code AS type_code',
            'matter.idx AS idx',
            DB::raw("MIN(pa_app.small_entity) = 1 AS sme_status"),
            'event.code AS event_name',
            'event.event_date AS event_date',
            'event.detail AS number',
            DB::raw("GROUP_CONCAT(DISTINCT pa_app.name SEPARATOR ', ') AS applicant_name"),
            'pa_cli.name AS client_name',
            'pmal_cli.actor_id AS client_id',
            'pa_cli.email AS email',
            DB::raw("IFNULL(task.assigned_to, matter.responsible) AS responsible"),
            'cla.value AS title',
            'ev.detail AS pub_num',
            'task.step AS step',
            'task.grace_period AS grace_period',
            'task.invoice_step AS invoice_step'
        )
        ->leftJoin(
            DB::raw('matter_actor_lnk pmal_app
            JOIN actor pa_app ON pa_app.id = pmal_app.actor_id'),
            function ($join) {
                $join->on(DB::raw('IFNULL(matter.container_id, matter.id)'), 'pmal_app.matter_id')
                ->where('pmal_app.role', 'APP');
            }
        )
        ->leftJoin(
            DB::raw('matter_actor_lnk pmal_cli
            JOIN actor pa_cli ON pa_cli.id = pmal_cli.actor_id'),
            function ($join) {
                $join->on(DB::raw('IFNULL(matter.container_id, matter.id)'), 'pmal_cli.matter_id')
                ->where('pmal_cli.role', 'CLI');
            }
        )
        ->leftJoin('country as mcountry', 'mcountry.iso', 'matter.country')
        ->join('event', 'matter.id', 'event.matter_id')
        ->leftJoin(
            'event AS ev',
            function ($join) {
                $join->on('matter.id', 'ev.matter_id')
                ->where('ev.code', 'PUB');
            }
        )
        ->join('task', 'task.trigger_id', 'event.id')
        ->leftJoin(
            'classifier AS cla',
            function ($join) {
                $join->on(DB::raw('IFNULL(matter.container_id, matter.id)'), 'cla.matter_id')
                ->where('cla.type_code', 'TITOF');
            }
        )
        ->leftJoin('fees', function ($join) {
            $join->on('fees.for_country', 'matter.country');
            $join->on('fees.for_category', 'matter.category_code');
            $join->on(DB::raw('CAST(task.detail AS UNSIGNED)'), 'fees.qt');
        })
        ->where('task.code', 'REN')
        ->where('matter.dead', 0)
        ->groupBy('task.due_date')
        ->groupBy('task.id');
        
        return $query;
    }
}
