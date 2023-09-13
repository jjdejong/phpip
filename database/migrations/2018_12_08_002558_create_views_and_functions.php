<?php

use Illuminate\Database\Migrations\Migration;

class CreateViewsAndFunctions extends Migration
{
    public function up()
    {
        DB::statement("CREATE
          VIEW `task_list` AS select `task`.`ID` AS `id`,
          `task`.`code` AS `code`,
          `event_name`.`name` AS `name`,
          `task`.`detail` AS `detail`,
          `task`.`due_date` AS `due_date`,
          `task`.`done` AS `done`,
          `task`.`done_date` AS `done_date`,
          `event`.`matter_ID` AS `matter_id`,
          `task`.`cost` AS `cost`,
          `task`.`fee` AS `fee`,
          `task`.`trigger_ID` AS `trigger_id`,
          `matter`.`category_code` AS `category`,
          `matter`.`caseref` AS `caseref`,
          `matter`.`country` AS `country`,
          `matter`.`origin` AS `origin`,
          `matter`.`type_code` AS `type_code`,
          `matter`.`idx` AS `idx`,
          ifnull(`task`.`assigned_to`,`matter`.`responsible`) AS `responsible`,
          `actor`.`login` AS `delegate`,
          `task`.`rule_used` AS `rule_used`,
          `matter`.`dead` AS `dead`
          from (((((`matter`
          left join `matter_actor_lnk` on(((ifnull(`matter`.`container_ID`,`matter`.`ID`) = `matter_actor_lnk`.`matter_ID`) and (`matter_actor_lnk`.`role` = 'DEL'))))
          left join `actor` on((`actor`.`ID` = `matter_actor_lnk`.`actor_ID`)))
          join `event` on((`matter`.`ID` = `event`.`matter_ID`)))
          join `task` on((`task`.`trigger_ID` = `event`.`ID`)))
          join `event_name` on((`task`.`code` = `event_name`.`code`)))"
        );

        DB::statement("CREATE
          VIEW `event_lnk_list` AS select `event`.`ID` AS `id`,
          `event`.`code` AS `code`,
          `event`.`matter_ID` AS `matter_id`,
          if(isnull(`event`.`alt_matter_ID`),`event`.`event_date`,`lnk`.`event_date`) AS `event_date`,
          if(isnull(`event`.`alt_matter_ID`),`event`.`detail`,`lnk`.`detail`) AS `detail`,
          `matter`.`country` AS `country`
          from ((`event`
          left join `event` `lnk` on(((`event`.`alt_matter_ID` = `lnk`.`matter_ID`) and (`lnk`.`code` = 'FIL'))))
          left join `matter` on((`event`.`alt_matter_ID` = `matter`.`ID`)))"
        );

        DB::unprepared("CREATE EVENT `kill_expired` ON SCHEDULE EVERY 1 WEEK ON COMPLETION PRESERVE DISABLE ON SLAVE COMMENT 'Updates the expired status of matters' DO CALL update_expired()");

        DB::unprepared("CREATE
          FUNCTION `actor_list`(mid INT, arole TEXT) RETURNS text CHARSET utf8
          BEGIN
          	DECLARE alist TEXT;
            SELECT GROUP_CONCAT(actor.name ORDER BY mal.display_order) INTO alist FROM matter_actor_lnk mal
            JOIN actor ON actor.ID = mal.actor_ID
            WHERE mal.matter_ID = mid AND mal.role = arole;
            RETURN alist;
          END"
        );

        DB::unprepared("CREATE
          FUNCTION `lowerword`( str TEXT, word VARCHAR(5) ) RETURNS text CHARSET utf8
          BEGIN
            DECLARE i INT DEFAULT 1;
            DECLARE loc INT;
            SET loc = LOCATE(CONCAT(word,' '), str, 2);
            IF loc > 1 THEN
              WHILE i <= LENGTH (str) AND loc <> 0 DO
                SET str = INSERT(str,loc,LENGTH(word),LCASE(word));
                SET i = loc+LENGTH(word);
                SET loc = LOCATE(CONCAT(word,' '), str, i);
              END WHILE;
            END IF;
            RETURN str;
          END"
        );

        DB::unprepared("CREATE
          FUNCTION `matter_status`(mid INT) RETURNS text CHARSET utf8
          BEGIN
          	DECLARE mstatus TEXT;
            SELECT CONCAT_WS(': ', event_name.name, status.event_date) INTO mstatus FROM `event` status
          	JOIN event_name ON mid=status.matter_ID AND event_name.code=status.code AND event_name.status_event=1
          	LEFT JOIN (event e2, event_name en2) ON e2.code=en2.code AND en2.status_event=1 AND mid=e2.matter_id AND status.event_date < e2.event_date
          	WHERE e2.matter_id IS NULL;
          	RETURN mstatus;
          END"
        );

        DB::unprepared("CREATE

          FUNCTION `tcase`( str TEXT) RETURNS text CHARSET utf8
          BEGIN
            DECLARE c CHAR(1);
            DECLARE s TEXT;
            DECLARE i INT DEFAULT 1;
            DECLARE bool INT DEFAULT 1;
            DECLARE punct CHAR(17) DEFAULT ' ()[]{},.-_!@;:?/';
            SET s = LCASE( str );
            WHILE i <= LENGTH( str ) DO
            	SET c = SUBSTRING( s, i, 1 );
            	IF LOCATE( c, punct ) > 0 THEN
            		SET bool = 1;
            	ELSEIF bool=1 THEN
                IF c >= 'a' AND c <= 'z' THEN
                  SET s = CONCAT(LEFT(s,i-1),UCASE(c),SUBSTRING(s,i+1));
                  SET bool = 0;
                ELSEIF c >= '0' AND c <= '9' THEN
                  SET bool = 0;
                END IF;
            	END IF;
            	SET i = i+1;
            END WHILE;
            SET s = lowerword(s, 'A');
            SET s = lowerword(s, 'An');
            SET s = lowerword(s, 'And');
            SET s = lowerword(s, 'As');
            SET s = lowerword(s, 'At');
            SET s = lowerword(s, 'But');
            SET s = lowerword(s, 'By');
            SET s = lowerword(s, 'For');
            SET s = lowerword(s, 'If');
            SET s = lowerword(s, 'In');
            SET s = lowerword(s, 'Of');
            SET s = lowerword(s, 'On');
            SET s = lowerword(s, 'Or');
            SET s = lowerword(s, 'The');
            SET s = lowerword(s, 'To');
            SET s = lowerword(s, 'Via');
            RETURN s;
          END"
        );

        DB::unprepared("CREATE
          VIEW `matter_actors` AS
          SELECT
            `pivot`.`id` AS `id`,
            `actor`.`id` AS `actor_id`,
            IFNULL(`actor`.`display_name`, `actor`.`name`) AS `display_name`,
            `actor`.`name` AS `name`,
            `actor`.`first_name` AS `first_name`,
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

        DB::unprepared("CREATE
          VIEW `matter_classifiers` AS
          SELECT
            `classifier`.`id` AS `id`,
            `matter`.`id` AS `matter_id`,
            `classifier`.`type_code` AS `type_code`,
            `classifier_type`.`type` AS `type_name`,
            `classifier_type`.`main_display` AS `main_display`,
            IF(ISNULL(`classifier`.`value_id`), `classifier`.`value`, `classifier_value`.`value`) AS `value`,
            `classifier`.`url` AS `url`,
            `classifier`.`lnk_matter_id` AS `lnk_matter_id`,
            `classifier`.`display_order` AS `display_order`
          FROM
            (((`classifier`
            JOIN `classifier_type` ON ((`classifier`.`type_code` = `classifier_type`.`code`)))
            JOIN `matter` ON ((IFNULL(`matter`.`container_id`, `matter`.`id`) = `classifier`.`matter_id`)))
            LEFT JOIN `classifier_value` ON ((`classifier_value`.`id` = `classifier`.`value_id`)))
          ORDER BY `classifier_type`.`display_order` , `classifier`.`display_order`;"
        );
    }

    public function down()
    {
        DB::unprepared("DROP VIEW IF EXISTS `task_list`");
        DB::unprepared("DROP VIEW IF EXISTS `event_lnk_list`");
        DB::unprepared("DROP FUNCTION IF EXISTS `actor_list`");
        DB::unprepared("DROP FUNCTION IF EXISTS `lowerword`");
        DB::unprepared("DROP FUNCTION IF EXISTS `matter_status`");
        DB::unprepared("DROP FUNCTION IF EXISTS `tcase`");
        DB::unprepared("DROP EVENT IF EXISTS `kill_expired`");
        DB::unprepared("DROP VIEW IF EXISTS `matter_classifiers`");
        DB::unprepared("DROP VIEW IF EXISTS `matter_actors`");
    }
}
