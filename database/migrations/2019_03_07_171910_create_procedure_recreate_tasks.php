<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreateProcedureRecreateTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("CREATE PROCEDURE `recreate_tasks`(IN Ptrigger_id INT)
proc: BEGIN
  DECLARE vevent_date, vdue_date, vbase_date, vexpiry, tmp_date DATE DEFAULT NULL;
  DECLARE vmatter_id, vid_uqtask, vrule_id, vdays, vmonths, vyears, vpta, vid, vcli_ann_agt INT DEFAULT NULL;
  DECLARE vevent, vtask, vtype, vcurrency CHAR(5) DEFAULT NULL;
  DECLARE vdetail, vresponsible VARCHAR(160) DEFAULT NULL;
  DECLARE done, vclear_task, vdelete_task, vend_of_month, vunique, vrecurring, vuse_parent, vuse_priority, vdead BOOLEAN DEFAULT 0;
  DECLARE vcost, vfee DECIMAL(6,2) DEFAULT null;

  DECLARE cur_rule CURSOR FOR
    SELECT task_rules.id, task, clear_task, delete_task, detail, days, months, years, recurring, end_of_month, use_parent, use_priority, cost, fee, currency, task_rules.responsible, event_name.unique
    FROM task_rules, event_name, matter
    WHERE vmatter_id=matter.id
    AND event_name.code=task
    AND vevent=trigger_event
    AND (for_category, ifnull(for_country, matter.country), ifnull(for_origin, matter.origin), ifnull(for_type, matter.type_code))<=>(matter.category_code, matter.country, matter.origin, matter.type_code)
	AND (uqtrigger=0
		OR (uqtrigger=1 AND NOT EXISTS (SELECT 1 FROM task_rules tr
		WHERE (tr.task, tr.for_category, tr.for_country)=(task_rules.task, matter.category_code, matter.country) AND tr.trigger_event!=task_rules.trigger_event)))
    AND NOT EXISTS (SELECT 1 FROM event WHERE matter_id=vmatter_id AND code=abort_on)
    AND (condition_event IS null OR EXISTS (SELECT 1 FROM event WHERE matter_id=vmatter_id AND code=condition_event))
    AND (vevent_date < use_before OR use_before IS null)
    AND (vevent_date > use_after OR use_after IS null)
    AND active=1;

  DECLARE cur_linked CURSOR FOR
	SELECT matter_id FROM event WHERE alt_matter_id=vmatter_id;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  DELETE from task where trigger_id = Ptrigger_id;

  SELECT matter_id, event_date, code INTO vmatter_id, vevent_date, vevent FROM event WHERE id = Ptrigger_id;
  SELECT type_code, dead, expire_date, term_adjust INTO vtype, vdead, vexpiry, vpta FROM matter WHERE matter.id=vmatter_id;
  SELECT id INTO vcli_ann_agt FROM actor WHERE display_name='CLIENT';

  IF (vdead OR Now() > vexpiry) THEN
    LEAVE proc;
  END IF;

  OPEN cur_rule;
  create_tasks: LOOP

	SET vid_uqtask=0;
	SET vbase_date = vevent_date;

  FETCH cur_rule INTO vrule_id, vtask, vclear_task, vdelete_task, vdetail, vdays, vmonths, vyears, vrecurring, vend_of_month, vuse_parent, vuse_priority, vcost, vfee, vcurrency, vresponsible, vunique;
  IF done THEN
    LEAVE create_tasks;
  END IF;

	IF (vtask='REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role='ANN' AND lnk.actor_id=vcli_ann_agt AND lnk.matter_id=vmatter_id)) THEN
		ITERATE create_tasks;
	END IF;

	IF vuse_parent THEN
		SELECT CAST(IFNULL(min(event_date), vevent_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=vmatter_id;
	END IF;

	IF vuse_priority THEN
		SELECT CAST(IFNULL(min(event_date), vevent_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PRI' AND matter_id=vmatter_id;
	END IF;

  IF vclear_task THEN
    UPDATE task, event SET task.done=1, task.done_date=vevent_date WHERE task.trigger_id=event.id AND task.code=vtask AND matter_id=vmatter_id AND done=0;
    ITERATE create_tasks;
  END IF;

  IF vdelete_task THEN
    DELETE task FROM event INNER JOIN task WHERE task.trigger_id=event.id AND task.code=vtask AND matter_id=vmatter_id;
    ITERATE create_tasks;
  END IF;

	IF (vunique OR vevent='PRI') THEN
		IF EXISTS (SELECT 1 FROM task, event WHERE event.id=task.trigger_id AND event.matter_id=vmatter_id AND task.rule_used=vrule_id) THEN
			SELECT task.id INTO vid_uqtask FROM task, event WHERE event.id=task.trigger_id AND event.matter_id=vmatter_id AND task.rule_used=vrule_id;
		END IF;
	END IF;

  IF (!vuse_parent AND !vuse_priority AND (vunique OR vevent='PRI') AND vid_uqtask > 0) THEN
    SELECT min(event_date) INTO vbase_date FROM event_lnk_list WHERE matter_id=vmatter_id AND code=vevent;
    IF vbase_date < vevent_date THEN

      ITERATE create_tasks;
    END IF;
  END IF;

  SET vdue_date = vbase_date + INTERVAL vdays DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
  IF vend_of_month THEN
    SET vdue_date=LAST_DAY(vdue_date);
  END IF;

	IF (vtask = 'REN' AND EXISTS (SELECT 1 FROM event WHERE code='PFIL' AND matter_id=vmatter_id) AND vdue_date < vevent_date) THEN
		SET vdue_date = vevent_date + INTERVAL 4 MONTH;
	END IF;


  IF (vdue_date < Now() AND vtask NOT IN ('EXP', 'REN')) OR (vdue_date < (Now() - INTERVAL 7 MONTH) AND vtask = 'REN') THEN
    ITERATE create_tasks;
  END IF;

  IF vtask='EXP' THEN
		UPDATE matter SET expire_date = vdue_date + INTERVAL vpta DAY WHERE matter.id=vmatter_id;
	ELSEIF vid_uqtask > 0 THEN
		UPDATE task SET trigger_id=Ptrigger_id, due_date=vdue_date WHERE id=vid_uqtask;
	ELSE
		INSERT INTO task (trigger_id,code,due_date,detail,rule_used,cost,fee,currency,assigned_to) values (Ptrigger_id,vtask,vdue_date,vdetail,vrule_id,vcost,vfee,vcurrency,vresponsible);
	END IF;

  END LOOP create_tasks;
  CLOSE cur_rule;

  SET done = 0;

  IF vevent = 'FIL' THEN
	OPEN cur_linked;
	recalc_linked: LOOP
		FETCH cur_linked INTO vid;
		IF done THEN
			LEAVE recalc_linked;
		END IF;
		CALL recalculate_tasks(vid, 'FIL');
	END LOOP recalc_linked;
	CLOSE cur_linked;
  END IF;

  IF vevent IN ('PRI', 'PFIL') THEN
    CALL recalculate_tasks(vmatter_id, 'FIL');
  END IF;

  SELECT killer INTO vdead FROM event_name WHERE vevent=event_name.code;
  IF vdead THEN
    UPDATE matter SET dead=1 WHERE matter.id=vmatter_id;
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
        DB::unprepared('DROP PROCEDURE IF EXISTS `recreate_tasks`');
    }
}
