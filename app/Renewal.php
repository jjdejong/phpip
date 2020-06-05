<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Renewal extends Model
{
  protected $table = 'renewal_list';
  protected $guarded = ['id'];

  public static function list() {
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
    DB::raw("(select 1 from `classifier` where `matter`.`id` = `classifier`.`matter_id` and `classifier`.`type_code` = 'SME') AS `sme_status`"),
    'event.code AS event_name',
    'event.event_date AS event_date',
    'event.detail AS number',
    DB::raw("group_concat(`pa_app`.`name` separator ',') AS `applicant_name`"),
    'pa_cli.name AS client_name',
    'pmal_cli.actor_id AS client_id',
    'pa_cli.email AS email',
    DB::raw("ifnull('task.assigned_to','matter.responsible') AS `responsible`"),
    'cla.value AS title',
    'ev.detail AS pub_num',
    'task.step AS step',
    'task.grace_period AS grace_period',
    'task.invoice_step AS invoice_step')
    ->leftJoin(
        DB::raw('matter_actor_lnk pmal_app
        JOIN actor pa_app ON pa_app.id = pmal_app.actor_id'),
        function ($join) {
            $join->on(DB::raw('ifnull(matter.container_id, matter.id)'), 'pmal_app.matter_id')->where('pmal_app.role', 'APP');
        }
    )
    ->leftJoin(
        DB::raw('matter_actor_lnk pmal_cli
        JOIN actor pa_cli ON pa_cli.id = pmal_cli.actor_id'),
        function ($join) {
            $join->on(DB::raw('ifnull(matter.container_id, matter.id)'), 'pmal_cli.matter_id')->where('pmal_cli.role', 'CLI');
        }
    )
    ->leftJoin('country as mcountry', 'mcountry.iso', 'matter.country')
    ->join('event', 'matter.id', 'event.matter_id')
    ->leftJoin('event as ev', function($join)
                         {   $join->on('matter.id', 'ev.matter_id');
                             $join->on('ev.code', '=', DB::raw("'PUB'"));
                         })
    ->join('task', 'task.trigger_id', 'event.id')
    ->leftJoin('classifier  AS cla', function($join)
                         {   $join->on(DB::raw('IFNULL(matter.container_id, matter.id)'), 'cla.matter_id');
                             $join->on('cla.type_code', '=', DB::raw("'TITOF'"));
                         })
    ->leftJoin('fees', function($join)
                         {   $join->on('fees.for_country', 'matter.country');
                             $join->on('fees.for_category', 'matter.category_code');
                             $join->on('fees.qt', 'task.detail');
                         })
    ->where('task.code','REN')
    ->where('matter.dead',0)
    ->groupBy('task.id');
    /*from ((((((((((`matter` left join `matter_actor_lnk` `pmal_app` on(ifnull(`matter`.`container_id`,`matter`.`id`) = `pmal_app`.`matter_id` and `pmal_app`.`role` = 'APP'))
    left join `actor` `pa_app` on(`pa_app`.`id` = `pmal_app`.`actor_id`))
    left join `matter_actor_lnk` `pmal_cli` on(ifnull(`matter`.`container_id`,`matter`.`id`) = `pmal_cli`.`matter_id` and `pmal_cli`.`role` = 'CLI'))
    left join `country` `mcountry` on(`mcountry`.`iso` = `matter`.`country`))
    left join `actor` `pa_cli` on(`pa_cli`.`id` = `pmal_cli`.`actor_id`))
    join `event` on(`matter`.`id` = `event`.`matter_id`))
    left join `event` `ev` on(`matter`.`id` = `ev`.`matter_id` and `ev`.`code` = 'PUB'))
    join `task` on(`task`.`trigger_id` = `event`.`id`))
    left join `classifier` `cla` on(ifnull(`matter`.`container_id`,`matter`.`id`) = `cla`.`matter_id` and `cla`.`type_code` = 'TITOF'))
    left join `fees` on(`fees`.`for_country` = `matter`.`country` and `fees`.`for_category` = `matter`.`category_code` and `fees`.`qt` = `task`.`detail`))
    where `task`.`code` = 'REN' and `matter`.`dead` = 0
    group by `task`.`id`)*/
    return $query;
  }
}
