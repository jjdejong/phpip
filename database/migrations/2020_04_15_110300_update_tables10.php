<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('actor', function (Blueprint $table) {
            $table->integer('pref_language')->after('warn')->unsigned();
            $table->float('ren_discount')->after('pref_language');
        });
        DB::unprepared('DROP VIEW IF EXISTS `renewal_list`');

        DB::statement("CREATE VIEW `renewal_list`  AS select
            `task`.`id` AS `id`,
            `task`.`detail` AS `detail`,
            `task`.`due_date` AS `due_date`,
            `task`.`done` AS `done`,
            `task`.`done_date` AS `done_date`,
            `event`.`matter_id` AS `matter_id`,
            `fees`.`cost` AS `cost`,
            `fees`.`fee` AS `fee`,
            `fees`.`cost_reduced` AS `cost_reduced`,
            `fees`.`fee_reduced` AS `fee_reduced`,
            `fees`.`cost_sup` AS `cost_sup`,
            `fees`.`fee_sup` AS `fee_sup`,
            `fees`.`fee_sup_reduced` AS `fee_sup_reduced`,
            `fees`.`cost_sup_reduced` AS `cost_sup_reduced`,
            `task`.`trigger_id` AS `trigger_id`,
            `matter`.`category_code` AS `category`,
            `matter`.`caseref` AS `caseref`,
            `matter`.`suffix` AS `suffix`,
            `matter`.`country` AS `country`,
            `mcountry`.`name_FR` AS `country_FR`,
            `matter`.`origin` AS `origin`
            ,`matter`.`type_code` AS `type_code`,
            `matter`.`idx` AS `idx`,
            (select 1 from `classifier` `sme` where `matter`.`id` = `sme`.`matter_id` and `sme`.`type_code` = 'SME') AS `sme_status`,
            `event`.`code` AS `event_name`,
            `event`.`event_date` AS `event_date`,
            `event`.`detail` AS `number`,
            group_concat(`pa_app`.`display_name` separator ',') AS `applicant_dn`,
            `pa_cli`.`display_name` AS `client_dn`,
            `pa_cli`.`ren_discount` AS `discount`,
            `pmal_cli`.`actor_id` AS `client_id`,
            `pa_cli`.`email` AS `email`,
            ifnull(`task`.`assigned_to`,`matter`.`responsible`) AS `responsible`,
            `cla`.`value` AS `title`,
            `ev`.`detail` AS `pub_num`,
            `task`.`step` AS `step`,
            `task`.`grace_period` AS `grace_period`,
            `task`.`invoice_step` AS `invoice_step`
            from ((((((((((`matter`
            left join `matter_actor_lnk` `pmal_app` on(ifnull(`matter`.`container_id`,`matter`.`id`) = `pmal_app`.`matter_id` and `pmal_app`.`role` = 'APP'))
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
            group by `task`.`id`"
        );
    }

    public function down()
    {
        Schema::table('actor', function (Blueprint $table) {
            if (Schema::hasColumn('actor', 'pref_language')) {
                $table->dropColumn('pref_language');
            }
            if (Schema::hasColumn('actor', 'ren_discount')) {
                $table->dropColumn('ren_discount');
            }
        });
    }
};
