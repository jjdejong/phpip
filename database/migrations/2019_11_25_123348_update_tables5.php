<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_insert`');
        DB::unprepared("CREATE TRIGGER `event_after_insert` AFTER INSERT ON `event` FOR EACH ROW
trig: BEGIN
  DECLARE vdue_date, vbase_date, vexpiry, tmp_date DATE DEFAULT NULL;
  DECLARE vcontainer_id, vid_uqtask, vrule_id, vdays, vmonths, vyears, vpta, vid, vcli_ann_agt INT DEFAULT NULL;
  DECLARE vtask, vtype, vcurrency CHAR(5) DEFAULT NULL;
  DECLARE vdetail, vresponsible VARCHAR(160) DEFAULT NULL;
  DECLARE done, vclear_task, vdelete_task, vend_of_month, vunique, vrecurring, vuse_parent, vuse_priority, vdead BOOLEAN DEFAULT 0;
  DECLARE vcost, vfee DECIMAL(6,2) DEFAULT null;
  DECLARE cur_rule CURSOR FOR
    SELECT task_rules.id, task, clear_task, delete_task, detail, days, months, years, recurring, end_of_month, use_parent, use_priority, cost, fee, currency, task_rules.responsible, event_name.`unique`
    FROM task_rules, event_name, matter
    WHERE NEW.matter_id=matter.id
    AND event_name.code=task
    AND NEW.code=trigger_event
    AND (for_category, ifnull(for_country, matter.country), ifnull(for_origin, matter.origin), ifnull(for_type, matter.type_code))<=>(matter.category_code, matter.country, matter.origin, matter.type_code)
    AND (uqtrigger=0
    OR (uqtrigger=1 AND NOT EXISTS (SELECT 1 FROM task_rules tr
    WHERE (tr.task, tr.for_category, tr.for_country)=(task_rules.task, matter.category_code, matter.country) AND tr.trigger_event!=task_rules.trigger_event)))
    AND NOT EXISTS (SELECT 1 FROM event WHERE matter_id=NEW.matter_id AND code=abort_on)
    AND (condition_event IS null OR EXISTS (SELECT 1 FROM event WHERE matter_id=NEW.matter_id AND code=condition_event))
    AND (NEW.event_date < use_before OR use_before IS null)
    AND (NEW.event_date > use_after OR use_after IS null)
    AND active=1;
  DECLARE cur_linked CURSOR FOR
   SELECT matter_id FROM event WHERE alt_matter_id=NEW.matter_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
  SELECT container_id, type_code, dead, expire_date, term_adjust INTO vcontainer_id, vtype, vdead, vexpiry, vpta FROM matter WHERE matter.id=NEW.matter_id;
  SELECT id INTO vcli_ann_agt FROM actor WHERE display_name='CLIENT';
  IF (vdead) THEN
    LEAVE trig;
  END IF;
  OPEN cur_rule;
  create_tasks: LOOP
    SET vid_uqtask=0;
    SET vbase_date = NEW.event_date;
    FETCH cur_rule INTO vrule_id, vtask, vclear_task, vdelete_task, vdetail, vdays, vmonths, vyears, vrecurring, vend_of_month, vuse_parent, vuse_priority, vcost, vfee, vcurrency, vresponsible, vunique;
    IF done THEN
      LEAVE create_tasks;
    END IF;
    IF (vtask='REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role='ANN' AND lnk.actor_id=vcli_ann_agt AND lnk.matter_id=NEW.matter_id)) THEN
      ITERATE create_tasks;
    END IF;
    IF vuse_parent THEN
      SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=NEW.matter_id;
    END IF;
    IF vuse_priority THEN
      SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PRI' AND matter_id=NEW.matter_id;
    END IF;
    IF vclear_task THEN
      UPDATE task, event SET task.done=1, task.done_date=NEW.event_date WHERE task.trigger_id=event.id AND task.code=vtask AND matter_id=NEW.matter_id AND done=0;
      ITERATE create_tasks;
    END IF;
    IF (vdelete_task AND vcontainer_id IS NOT NULL) THEN
      DELETE task FROM event INNER JOIN task WHERE task.trigger_id=event.id AND task.code=vtask AND matter_id=NEW.matter_id;
      ITERATE create_tasks;
    END IF;
    IF (vunique OR NEW.code='PRI') THEN
      IF EXISTS (SELECT 1 FROM task JOIN event ON event.id=task.trigger_id WHERE event.matter_id=NEW.matter_id AND task.rule_used=vrule_id) THEN
        SELECT task.id INTO vid_uqtask FROM task JOIN event ON event.id=task.trigger_id WHERE event.matter_id=NEW.matter_id AND task.rule_used=vrule_id;
      END IF;
    END IF;
    IF (!vuse_parent AND !vuse_priority AND (vunique OR NEW.code='PRI') AND vid_uqtask > 0) THEN
      SELECT min(event_date) INTO vbase_date FROM event_lnk_list WHERE matter_id=NEW.matter_id AND code=NEW.code;
      IF vbase_date < NEW.event_date THEN
        ITERATE create_tasks;
      END IF;
    END IF;
    SET vdue_date = vbase_date + INTERVAL vdays DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
    IF vend_of_month THEN
      SET vdue_date=LAST_DAY(vdue_date);
    END IF;
    IF (vtask = 'REN' AND EXISTS (SELECT 1 FROM event WHERE code='PFIL' AND matter_id=NEW.matter_id) AND vdue_date < NEW.event_date) THEN
      SET vdue_date = NEW.event_date + INTERVAL 4 MONTH;
    END IF;
    IF (vdue_date < Now() AND vtask NOT IN ('EXP', 'REN')) OR (vdue_date < (Now() - INTERVAL 7 MONTH) AND vtask = 'REN') THEN
      ITERATE create_tasks;
    END IF;
    IF vtask='EXP' THEN
      UPDATE matter SET expire_date = vdue_date + INTERVAL vpta DAY WHERE matter.id=NEW.matter_id;
    ELSEIF vid_uqtask > 0 THEN
      UPDATE task SET trigger_id=NEW.id, due_date=vdue_date, updater=NEW.creator, updated_at=Now() WHERE id=vid_uqtask;
    ELSE
      INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
      VALUES (NEW.id, vtask, vdue_date, vdetail, vrule_id, vcost, vfee, vcurrency, vresponsible, NEW.creator, Now(), Now());
    END IF;
  END LOOP create_tasks;
  CLOSE cur_rule;
  SET done = 0;
  IF NEW.code = 'FIL' THEN
    OPEN cur_linked;
    recalc_linked: LOOP
      FETCH cur_linked INTO vid;
      IF done THEN
        LEAVE recalc_linked;
      END IF;
      CALL recalculate_tasks(vid, 'FIL', NEW.creator);
    END LOOP recalc_linked;
    CLOSE cur_linked;
  END IF;
  IF NEW.code IN ('PRI', 'PFIL') THEN
    CALL recalculate_tasks(NEW.matter_id, 'FIL', NEW.creator);
  END IF;
  SELECT killer INTO vdead FROM event_name WHERE NEW.code=event_name.code;
  IF vdead THEN
    UPDATE matter SET dead = 1 WHERE matter.id=NEW.matter_id;
  END IF;
END trig");

        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_update`');
        DB::unprepared("CREATE TRIGGER `event_after_update` AFTER UPDATE ON `event` FOR EACH ROW
trig: BEGIN
  DECLARE vdue_date, vbase_date DATE DEFAULT NULL;
  DECLARE vtask_id, vdays, vmonths, vyears, vrecurring, vpta, vid INT DEFAULT NULL;
  DECLARE done, vend_of_month, vunique, vuse_parent, vuse_priority BOOLEAN DEFAULT 0;
  DECLARE vcategory, vcountry CHAR(5) DEFAULT NULL;
  DECLARE cur_rule CURSOR FOR
	SELECT task.id, days, months, years, recurring, end_of_month, use_parent, use_priority
	FROM task_rules, task
	WHERE task.rule_used=task_rules.id
	AND task.trigger_id=NEW.id;
  DECLARE cur_linked CURSOR FOR
   SELECT matter_id FROM event WHERE alt_matter_id=NEW.matter_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
  IF (OLD.event_date = NEW.event_date AND NEW.alt_matter_id <=> OLD.alt_matter_id) THEN
	 LEAVE trig;
  END IF;
  SET vbase_date=NEW.event_date;
  OPEN cur_rule;
  update_tasks: LOOP
  	FETCH cur_rule INTO vtask_id, vdays, vmonths, vyears, vrecurring, vend_of_month, vuse_parent, vuse_priority;
  	IF done THEN
  	  LEAVE update_tasks;
  	END IF;
  	IF vuse_parent THEN
  	  SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=NEW.matter_id;
  	END IF;
  	IF vuse_priority THEN
  	  SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PRI' AND matter_id=NEW.matter_id;
  	END IF;
  	SET vdue_date = vbase_date + INTERVAL vdays DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
  	IF vend_of_month THEN
  	  SET vdue_date=LAST_DAY(vdue_date);
  	END IF;
  	UPDATE task set due_date=vdue_date, updater=NEW.updater, updated_at=Now() WHERE id=vtask_id;
  END LOOP update_tasks;
  CLOSE cur_rule;
  SET done = 0;
  IF NEW.code = 'FIL' THEN
  	OPEN cur_linked;
  	recalc_linked: LOOP
  	  FETCH cur_linked INTO vid;
  	  IF done THEN
  		  LEAVE recalc_linked;
  	  END IF;
  	  CALL recalculate_tasks(vid, 'FIL', NEW.updater);
  	  CALL recalculate_tasks(vid, 'PRI', NEW.updater);
  	END LOOP recalc_linked;
  	CLOSE cur_linked;
  END IF;
  IF NEW.code IN ('PRI', 'PFIL') THEN
  	CALL recalculate_tasks(NEW.matter_id, 'FIL', NEW.updater);
  END IF;
  IF NEW.code IN ('FIL', 'PFIL') THEN
  	SELECT category_code, term_adjust, country INTO vcategory, vpta, vcountry FROM matter WHERE matter.id=NEW.matter_id;
  	SELECT months, years INTO vmonths, vyears FROM task_rules
  	 WHERE task='EXP'
  	 AND for_category=vcategory
  	 AND (for_country=vcountry OR (for_country IS NULL AND NOT EXISTS (SELECT 1 FROM task_rules tr WHERE task_rules.task=tr.task AND for_country=vcountry)));
  	SELECT IFNULL(min(event_date), NEW.event_date) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=NEW.matter_id;
  	SET vdue_date = vbase_date + INTERVAL vpta DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
  	UPDATE matter SET expire_date=vdue_date WHERE matter.id=NEW.matter_id;
  END IF;
END trig");

        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_delete`');
        DB::unprepared("CREATE TRIGGER `event_after_delete` AFTER DELETE ON `event` FOR EACH ROW
BEGIN
  IF OLD.code IN ('PRI','PFIL') THEN
	 CALL recalculate_tasks(OLD.matter_id, 'FIL', OLD.updater);
  END IF;
  IF OLD.code='FIL' THEN
  	 UPDATE matter SET expire_date=NULL WHERE matter.id=OLD.matter_id;
  END IF;
  UPDATE matter
   JOIN event_name ON (OLD.code=event_name.code)
   SET matter.dead=0
   WHERE matter.id=OLD.matter_id
   AND NOT EXISTS (SELECT 1 FROM event JOIN event_name en ON (event.code=en.code) WHERE event.matter_id=OLD.matter_id AND en.killer=1);
END");

        DB::unprepared('DROP PROCEDURE IF EXISTS `recalculate_tasks`');
        DB::unprepared("CREATE PROCEDURE `recalculate_tasks`(IN Pmatter_id int, IN Ptrig_code char(5), IN Puser char(16))
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
		UPDATE task set due_date=vdue_date, updated_at=Now(), updater=Puser WHERE task.id=vtask_id;
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
END proc");

        DB::unprepared('DROP PROCEDURE IF EXISTS `recreate_tasks`');
        DB::unprepared("CREATE PROCEDURE `recreate_tasks`(IN Ptrigger_id INT, Puser char(16))
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
		UPDATE task SET trigger_id=Ptrigger_id, due_date=vdue_date, updater=Puser, updated_at=Now() WHERE id=vid_uqtask;
	ELSE
		INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
        VALUES (Ptrigger_id, vtask, vdue_date, vdetail, vrule_id, vcost, vfee, vcurrency, vresponsible, Puser, Now(), Now());
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
		CALL recalculate_tasks(vid, 'FIL', Puser);
	END LOOP recalc_linked;
	CLOSE cur_linked;
  END IF;
  IF vevent IN ('PRI', 'PFIL') THEN
    CALL recalculate_tasks(vmatter_id, 'FIL', Puser);
  END IF;
  SELECT killer INTO vdead FROM event_name WHERE vevent=event_name.code;
  IF vdead THEN
    UPDATE matter SET dead=1 WHERE matter.id=vmatter_id;
  END IF;
END proc");
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_insert`');
        DB::unprepared("CREATE TRIGGER `event_after_insert` AFTER INSERT ON `event` FOR EACH ROW
trig: BEGIN
  DECLARE vdue_date, vbase_date, vexpiry, tmp_date DATE DEFAULT NULL;
  DECLARE vcontainer_id, vid_uqtask, vrule_id, vdays, vmonths, vyears, vpta, vid, vcli_ann_agt INT DEFAULT NULL;
  DECLARE vtask, vtype, vcurrency CHAR(5) DEFAULT NULL;
  DECLARE vdetail, vresponsible VARCHAR(160) DEFAULT NULL;
  DECLARE done, vclear_task, vdelete_task, vend_of_month, vunique, vrecurring, vuse_parent, vuse_priority, vdead BOOLEAN DEFAULT 0;
  DECLARE vcost, vfee DECIMAL(6,2) DEFAULT null;
  DECLARE cur_rule CURSOR FOR
    SELECT task_rules.id, task, clear_task, delete_task, detail, days, months, years, recurring, end_of_month, use_parent, use_priority, cost, fee, currency, task_rules.responsible, event_name.`unique`
    FROM task_rules, event_name, matter
    WHERE NEW.matter_id=matter.id
    AND event_name.code=task
    AND NEW.code=trigger_event
    AND (for_category, ifnull(for_country, matter.country), ifnull(for_origin, matter.origin), ifnull(for_type, matter.type_code))<=>(matter.category_code, matter.country, matter.origin, matter.type_code)
    AND (uqtrigger=0
    OR (uqtrigger=1 AND NOT EXISTS (SELECT 1 FROM task_rules tr
    WHERE (tr.task, tr.for_category, tr.for_country)=(task_rules.task, matter.category_code, matter.country) AND tr.trigger_event!=task_rules.trigger_event)))
    AND NOT EXISTS (SELECT 1 FROM event WHERE matter_id=NEW.matter_id AND code=abort_on)
    AND (condition_event IS null OR EXISTS (SELECT 1 FROM event WHERE matter_id=NEW.matter_id AND code=condition_event))
    AND (NEW.event_date < use_before OR use_before IS null)
    AND (NEW.event_date > use_after OR use_after IS null)
    AND active=1;
  DECLARE cur_linked CURSOR FOR
   SELECT matter_id FROM event WHERE alt_matter_id=NEW.matter_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
  SELECT container_id, type_code, dead, expire_date, term_adjust INTO vcontainer_id, vtype, vdead, vexpiry, vpta FROM matter WHERE matter.id=NEW.matter_id;
  SELECT id INTO vcli_ann_agt FROM actor WHERE display_name='CLIENT';
  IF (vdead) THEN
    LEAVE trig;
  END IF;
  OPEN cur_rule;
  create_tasks: LOOP
    SET vid_uqtask=0;
    SET vbase_date = NEW.event_date;
    FETCH cur_rule INTO vrule_id, vtask, vclear_task, vdelete_task, vdetail, vdays, vmonths, vyears, vrecurring, vend_of_month, vuse_parent, vuse_priority, vcost, vfee, vcurrency, vresponsible, vunique;
    IF done THEN
      LEAVE create_tasks;
    END IF;
    IF (vtask='REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role='ANN' AND lnk.actor_id=vcli_ann_agt AND lnk.matter_id=NEW.matter_id)) THEN
      ITERATE create_tasks;
    END IF;
    IF vuse_parent THEN
      SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=NEW.matter_id;
    END IF;
    IF vuse_priority THEN
      SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PRI' AND matter_id=NEW.matter_id;
    END IF;
    IF vclear_task THEN
      UPDATE task, event SET task.done=1, task.done_date=NEW.event_date WHERE task.trigger_id=event.id AND task.code=vtask AND matter_id=NEW.matter_id AND done=0;
      ITERATE create_tasks;
    END IF;
    IF (vdelete_task AND vcontainer_id IS NOT NULL) THEN
      DELETE task FROM event INNER JOIN task WHERE task.trigger_id=event.id AND task.code=vtask AND matter_id=NEW.matter_id;
      ITERATE create_tasks;
    END IF;
    IF (vunique OR NEW.code='PRI') THEN
      IF EXISTS (SELECT 1 FROM task JOIN event ON event.id=task.trigger_id WHERE event.matter_id=NEW.matter_id AND task.rule_used=vrule_id) THEN
        SELECT task.id INTO vid_uqtask FROM task JOIN event ON event.id=task.trigger_id WHERE event.matter_id=NEW.matter_id AND task.rule_used=vrule_id;
      END IF;
    END IF;
    IF (!vuse_parent AND !vuse_priority AND (vunique OR NEW.code='PRI') AND vid_uqtask > 0) THEN
      SELECT min(event_date) INTO vbase_date FROM event_lnk_list WHERE matter_id=NEW.matter_id AND code=NEW.code;
      IF vbase_date < NEW.event_date THEN
        ITERATE create_tasks;
      END IF;
    END IF;
    SET vdue_date = vbase_date + INTERVAL vdays DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
    IF vend_of_month THEN
      SET vdue_date=LAST_DAY(vdue_date);
    END IF;
    IF (vtask = 'REN' AND EXISTS (SELECT 1 FROM event WHERE code='PFIL' AND matter_id=NEW.matter_id) AND vdue_date < NEW.event_date) THEN
      SET vdue_date = NEW.event_date + INTERVAL 4 MONTH;
    END IF;
    IF (vdue_date < Now() AND vtask NOT IN ('EXP', 'REN')) OR (vdue_date < (Now() - INTERVAL 7 MONTH) AND vtask = 'REN') THEN
      ITERATE create_tasks;
    END IF;
    IF vtask='EXP' THEN
      UPDATE matter SET expire_date = vdue_date + INTERVAL vpta DAY WHERE matter.id=NEW.matter_id;
    ELSEIF vid_uqtask > 0 THEN
      UPDATE task SET trigger_id=NEW.id, due_date=vdue_date WHERE id=vid_uqtask;
    ELSE
      INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
      VALUES (NEW.id, vtask,vdue_date, vdetail, vrule_id, vcost,vfee, vcurrency, vresponsible, NEW.creator, Now(), Now());
    END IF;
  END LOOP create_tasks;
  CLOSE cur_rule;
  SET done = 0;
  IF NEW.code = 'FIL' THEN
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
  IF NEW.code IN ('PRI', 'PFIL') THEN
    CALL recalculate_tasks(NEW.matter_id, 'FIL');
  END IF;
  SELECT killer INTO vdead FROM event_name WHERE NEW.code=event_name.code;
  IF vdead THEN
    UPDATE matter SET dead = 1 WHERE matter.id=NEW.matter_id;
  END IF;
END trig");

        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_update`');
        DB::unprepared("CREATE TRIGGER `event_after_update` AFTER UPDATE ON `event` FOR EACH ROW
trig: BEGIN
  DECLARE vdue_date, vbase_date DATE DEFAULT NULL;
  DECLARE vtask_id, vdays, vmonths, vyears, vrecurring, vpta, vid INT DEFAULT NULL;
  DECLARE done, vend_of_month, vunique, vuse_parent, vuse_priority BOOLEAN DEFAULT 0;
  DECLARE vcategory, vcountry CHAR(5) DEFAULT NULL;
  DECLARE cur_rule CURSOR FOR
  SELECT task.id, days, months, years, recurring, end_of_month, use_parent, use_priority
  FROM task_rules, task
  WHERE task.rule_used=task_rules.id
  AND task.trigger_id=NEW.id;
  DECLARE cur_linked CURSOR FOR
   SELECT matter_id FROM event WHERE alt_matter_id=NEW.matter_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
  IF (OLD.event_date = NEW.event_date AND NEW.alt_matter_id <=> OLD.alt_matter_id) THEN
  LEAVE trig;
  END IF;
  SET vbase_date=NEW.event_date;
  OPEN cur_rule;
  update_tasks: LOOP
    FETCH cur_rule INTO vtask_id, vdays, vmonths, vyears, vrecurring, vend_of_month, vuse_parent, vuse_priority;
    IF done THEN
      LEAVE update_tasks;
    END IF;
    IF vuse_parent THEN
      SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=NEW.matter_id;
    END IF;
    IF vuse_priority THEN
      SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PRI' AND matter_id=NEW.matter_id;
    END IF;
    SET vdue_date = vbase_date + INTERVAL vdays DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
    IF vend_of_month THEN
      SET vdue_date=LAST_DAY(vdue_date);
    END IF;
    UPDATE task set due_date=vdue_date, updater=NEW.updater, updated_at=Now() WHERE id=vtask_id;
  END LOOP update_tasks;
  CLOSE cur_rule;
  SET done = 0;
  IF NEW.code = 'FIL' THEN
    OPEN cur_linked;
    recalc_linked: LOOP
      FETCH cur_linked INTO vid;
      IF done THEN
        LEAVE recalc_linked;
      END IF;
      CALL recalculate_tasks(vid, 'FIL');
      CALL recalculate_tasks(vid, 'PRI');
    END LOOP recalc_linked;
    CLOSE cur_linked;
  END IF;
  IF NEW.code IN ('PRI', 'PFIL') THEN
    CALL recalculate_tasks(NEW.matter_id, 'FIL');
  END IF;
  IF NEW.code IN ('FIL', 'PFIL') THEN
    SELECT category_code, term_adjust, country INTO vcategory, vpta, vcountry FROM matter WHERE matter.id=NEW.matter_id;
    SELECT months, years INTO vmonths, vyears FROM task_rules
      WHERE task='EXP'
      AND for_category=vcategory
      AND (for_country=vcountry OR (for_country IS NULL AND NOT EXISTS (SELECT 1 FROM task_rules tr WHERE task_rules.task=tr.task AND for_country=vcountry)));
    SELECT IFNULL(min(event_date), NEW.event_date) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=NEW.matter_id;
    SET vdue_date = vbase_date + INTERVAL vpta DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
    UPDATE matter SET expire_date=vdue_date WHERE matter.id=NEW.matter_id;
  END IF;
END trig");

        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_delete`');
        DB::unprepared("CREATE TRIGGER `event_after_delete` AFTER DELETE ON `event` FOR EACH ROW
BEGIN
  IF OLD.code IN ('PRI','PFIL') THEN
    CALL recalculate_tasks(OLD.matter_id, 'FIL');
  END IF;
  IF OLD.code='FIL' THEN
    UPDATE matter SET expire_date=NULL WHERE matter.id=OLD.matter_id;
  END IF;
  UPDATE matter
  JOIN event_name ON (OLD.code=event_name.code)
    SET matter.dead=0
    WHERE matter.id=OLD.matter_id
    AND NOT EXISTS (SELECT 1 FROM event JOIN event_name en ON (event.code=en.code) WHERE event.matter_id=OLD.matter_id AND en.killer=1);
END");

        DB::unprepared('DROP PROCEDURE IF EXISTS `recalculate_tasks`');
        DB::unprepared("CREATE PROCEDURE `recalculate_tasks`(IN Pmatter_id int, IN Ptrig_code char(5))
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
    UPDATE task set due_date=vdue_date, updated_at=Now() WHERE task.id=vtask_id;
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
END proc");

        DB::unprepared('DROP PROCEDURE IF EXISTS `recreate_tasks`');
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
		UPDATE task SET trigger_id=Ptrigger_id, due_date=vdue_date, updated_at=Now() WHERE id=vid_uqtask;
	ELSE
		INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
        VALUES (Ptrigger_id, vtask, vdue_date, vdetail, vrule_id, vcost, vfee, vcurrency, vresponsible, 'phpip', Now(), Now());
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
END proc");
    }
};
