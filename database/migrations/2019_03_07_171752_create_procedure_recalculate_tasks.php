<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureRecalculateTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
          "CREATE DEFINER=`root`@`localhost` PROCEDURE `recalculate_tasks`(IN Pmatter_id int, IN Ptrig_code char(5))
          proc: BEGIN
          	DECLARE vtrigevent_date, vdue_date, vbase_date DATE DEFAULT NULL;
          	DECLARE vtask_id, vtrigevent_id, vdays, vmonths, vyears, vrecurring, vpta INT DEFAULT NULL;
          	DECLARE done, vend_of_month, vunique, vuse_parent, vuse_priority BOOLEAN DEFAULT 0;
          	DECLARE vcategory, vcountry CHAR(5) DEFAULT NULL;

          	DECLARE cur_rule CURSOR FOR
          		SELECT task.id, days, months, years, recurring, end_of_month, use_parent, use_priority
          		FROM task_rules, task
          		WHERE task.rule_used=task_rules.id
          		AND task.trigger_id=vtrigevent_id;

          	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

          	IF EXISTS (SELECT 1 FROM event_lnk_list WHERE matter_id=Pmatter_id AND code=Ptrig_code) THEN
          		SELECT id, event_date INTO vtrigevent_id, vtrigevent_date FROM event_lnk_list WHERE matter_id=Pmatter_id AND code=Ptrig_code ORDER BY event_date LIMIT 1;
          	ELSE
          		LEAVE proc;
          	END IF;

          	OPEN cur_rule;
          	update_tasks: LOOP
          		FETCH cur_rule INTO vtask_id, vdays, vmonths, vyears, vrecurring, vend_of_month, vuse_parent, vuse_priority;
          		IF done THEN
          			LEAVE update_tasks;
          		END IF;

          		IF vuse_parent THEN
          			SELECT CAST(IFNULL(min(event_date), vtrigevent_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=Pmatter_id;
          		ELSE
          			SET vbase_date=vtrigevent_date;
          		END IF;

          		IF vuse_priority THEN
          			SELECT CAST(IFNULL(min(event_date), vtrigevent_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PRI' AND matter_id=Pmatter_id;
          		END IF;

          		SET vdue_date = vbase_date + INTERVAL vdays DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
          		IF vend_of_month THEN
          			SET vdue_date=LAST_DAY(vdue_date);
          		END IF;

          		UPDATE task set due_date=vdue_date WHERE task.id=vtask_id;

          	END LOOP update_tasks;
          	CLOSE cur_rule;


          	IF Ptrig_code = 'FIL' THEN
          		SELECT category_code, term_adjust, country INTO vcategory, vpta, vcountry FROM matter WHERE matter.id=Pmatter_id;
          		SELECT months, years INTO vmonths, vyears FROM task_rules
          			WHERE task='EXP'
          			AND for_category=vcategory
          			AND (for_country=vcountry OR (for_country IS NULL AND NOT EXISTS (SELECT 1 FROM task_rules tr WHERE task_rules.task=tr.task AND for_country=vcountry)));
          		SELECT CAST(IFNULL(min(event_date), vtrigevent_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=Pmatter_id;
          		SET vdue_date = vbase_date + INTERVAL vpta DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
          		UPDATE matter SET expire_date=vdue_date WHERE matter.id=Pmatter_id AND IFNULL(expire_date, '0000-00-00') != vdue_date;
          	END IF;

          END proc"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `recalculate_tasks`');
    }
}
