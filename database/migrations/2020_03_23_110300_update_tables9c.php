<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class UpdateTables9c extends Migration
{
    public function up()
    {
        DB::unprepared("DROP VIEW IF EXISTS `matter_actors`");

        DB::unprepared("CREATE
          VIEW `matter_actors` AS
          SELECT
            `pivot`.`id` AS `id`,
            `actor`.`id` AS `actor_id`,
            IFNULL(`actor`.`display_name`, `actor`.`name`) AS `display_name`,
            `actor`.`name` AS `name`,
            `actor`.`first_name` AS `first_name`,
            `actor`.`email` AS `email`,
            `pivot`.`display_order` AS `display_order`,
            `pivot`.`role` AS `role_code`,
            `actor_role`.`name` AS `role_name`,
            `actor_role`.`shareable` AS `shareable`,
            `actor_role`.`show_ref` AS `show_ref`,
            `actor_role`.`show_company` AS `show_company`,
            `actor_role`.`show_rate` AS `show_rate`,
            `actor_role`.`show_date` AS `show_date`,
            `matter`.`id` AS `matter_id`,
            `actor`.`warn` AS `warn`,
            `pivot`.`actor_ref` AS `actor_ref`,
            `pivot`.`date` AS `date`,
            `pivot`.`rate` AS `rate`,
            `pivot`.`shared` AS `shared`,
            `co`.`name` AS `company`,
            IF((`pivot`.`matter_id` = `matter`.`container_id`), 1, 0) AS `inherited`
          FROM
            ((((`matter_actor_lnk` `pivot`
            JOIN `matter` ON (((`pivot`.`matter_id` = `matter`.`id`)
              OR ((`pivot`.`shared` = 1)
              AND (`pivot`.`matter_id` = `matter`.`container_id`)))))
            JOIN `actor` ON ((`pivot`.`actor_id` = `actor`.`id`)))
            LEFT JOIN `actor` `co` ON ((`co`.`id` = `pivot`.`company_id`)))
            JOIN `actor_role` ON ((`pivot`.`role` = `actor_role`.`code`)))
          ORDER BY `actor_role`.`display_order` , `pivot`.`display_order`;"
        );
    }

    public function down()
    {
        //
    }
}
