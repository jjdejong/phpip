<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   // For actor
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `actor_creator_log` BEFORE INSERT ON `actor` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `actor_updater_log` BEFORE UPDATE ON `actor` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1);");
        // For actor_role
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `arole_create_log` BEFORE INSERT ON `actor_role` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `arole_modify_log` BEFORE UPDATE ON `actor_role` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1)");
        // For classifier
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `classifier_BEFORE_INSERT` BEFORE INSERT ON `classifier` FOR EACH ROW
BEGIN
	SET NEW.creator=SUBSTRING_INDEX(USER(),'@',1);
    IF NEW.type_code = 'TITEN' THEN
		SET NEW.value=tcase(NEW.value);
	ELSEIF NEW.type_code IN ('TIT', 'TITOF', 'TITAL') THEN
		SET NEW.value=CONCAT(UCASE(SUBSTR(NEW.value, 1, 1)),LCASE(SUBSTR(NEW.value FROM 2)));
	END IF;
END;");
        DB::unprepared( "CREATE DEFINER = `root`@`localhost` TRIGGER `classifier_updater_log` BEFORE UPDATE ON `classifier` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1);");
        // For classifier_type
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `ctype_creator_log` BEFORE INSERT ON `classifier_type` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `ctype_updater_log` BEFORE UPDATE ON `classifier_type` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1);");
        // For classifier value
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `cvalue_creator_log` BEFORE INSERT ON `classifier_value` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `cvalue_updater_log` BEFORE UPDATE ON `classifier_value` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1)");
        // For event
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `event_before_insert` BEFORE INSERT ON `event` FOR EACH ROW BEGIN
	DECLARE vdate DATE DEFAULT NULL;

	SET new.creator = SUBSTRING_INDEX(USER(),'@',1);

	IF NEW.alt_matter_id IS NOT NULL THEN
		IF EXISTS (SELECT 1 FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id AND event_date IS NOT NULL) THEN
			SELECT event_date INTO vdate FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id;
			SET NEW.event_date = vdate;
		ELSE
			SET NEW.event_date = Now();
		END IF;
	END IF;
END");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `event_after_insert` AFTER INSERT ON `event` FOR EACH ROW trig: BEGIN
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

  -- Do not change anything in dead cases
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

	-- Skip renewal tasks if the client is the annuity agent
	IF (vtask='REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role='ANN' AND lnk.actor_id=vcli_ann_agt AND lnk.matter_id=NEW.matter_id)) THEN
		ITERATE create_tasks;
	END IF;

	IF vuse_parent THEN
	-- Use parent filing date for task deadline calculation, if PFIL event exists
		SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PFIL' AND matter_id=NEW.matter_id;
	END IF;

	IF vuse_priority THEN
	-- Use earliest priority date for task deadline calculation, if a PRI event exists
		SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO vbase_date FROM event_lnk_list WHERE code='PRI' AND matter_id=NEW.matter_id;
	END IF;

    IF vclear_task THEN
    -- Clear the task identified by the rule (do not create anything)
      UPDATE task, event SET task.done=1, task.done_date=NEW.event_date WHERE task.trigger_id=event.id AND task.code=vtask AND matter_id=NEW.matter_id AND done=0;
      ITERATE create_tasks;
    END IF;

    IF (vdelete_task AND vcontainer_id IS NOT NULL) THEN
    -- Delete the task identified by the rule, only if the matter is a new family member (do not create anything)
      DELETE task FROM event INNER JOIN task WHERE task.trigger_id=event.id AND task.code=vtask AND matter_id=NEW.matter_id;
      ITERATE create_tasks;
    END IF;

    -- If the new event is unique or the event is a priority claim, retrieve a similar event if it already exists
	IF (vunique OR NEW.code='PRI') THEN
		IF EXISTS (SELECT 1 FROM task JOIN event ON event.id=task.trigger_id WHERE event.matter_id=NEW.matter_id AND task.rule_used=vrule_id) THEN
			SELECT task.id INTO vid_uqtask FROM task JOIN event ON event.id=task.trigger_id WHERE event.matter_id=NEW.matter_id AND task.rule_used=vrule_id;
		END IF;
	END IF;

	-- If the unique event or a priority claim already exists and its date is earlier than the new date, do nothing. If its date is later than the new date, proceed with the new date
    IF (!vuse_parent AND !vuse_priority AND (vunique OR NEW.code='PRI') AND vid_uqtask > 0) THEN
      SELECT min(event_date) INTO vbase_date FROM event_lnk_list WHERE matter_id=NEW.matter_id AND code=NEW.code;
      IF vbase_date < NEW.event_date THEN
        ITERATE create_tasks;
      END IF;
    END IF;

	-- Calculate the deadline
    SET vdue_date = vbase_date + INTERVAL vdays DAY + INTERVAL vmonths MONTH + INTERVAL vyears YEAR;
    IF vend_of_month THEN
      SET vdue_date=LAST_DAY(vdue_date);
    END IF;

	-- Deadline for renewals that have become due in a parent case when filing a divisional (EP 4-months rule)
	IF (vtask = 'REN' AND EXISTS (SELECT 1 FROM event WHERE code='PFIL' AND matter_id=NEW.matter_id) AND vdue_date < NEW.event_date) THEN
		SET vdue_date = NEW.event_date + INTERVAL 4 MONTH;
	END IF;

	-- Skip creating tasks having a deadline too far in the past, except if the event is the expiry
    IF (vdue_date < Now() AND vtask NOT IN ('EXP', 'REN')) OR (vdue_date < (Now() - INTERVAL 7 MONTH) AND vtask = 'REN') THEN
      ITERATE create_tasks;
    END IF;

	-- Handle the expiry task (no task created). Otherwise replace an existing unique task with the current (unique) task, or insert the new (non-unique) task
    IF vtask='EXP' THEN
		UPDATE matter SET expire_date = vdue_date + INTERVAL vpta DAY WHERE matter.id=NEW.matter_id;
	ELSEIF vid_uqtask > 0 THEN
		UPDATE task SET trigger_id=NEW.id, due_date=vdue_date WHERE id=vid_uqtask;
	ELSE
		INSERT INTO task (trigger_id,code,due_date,detail,rule_used,cost,fee,currency,assigned_to) values (NEW.id,vtask,vdue_date,vdetail,vrule_id,vcost,vfee,vcurrency,vresponsible);
	END IF;

  END LOOP create_tasks;
  CLOSE cur_rule;

  SET done = 0;

  -- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
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

  -- Recalculate tasks based on the priority date or a parent case upon inserting a new priority claim or a parent filed event
  IF NEW.code IN ('PRI', 'PFIL') THEN
    CALL recalculate_tasks(NEW.matter_id, 'FIL');
  END IF;

  -- Set matter to dead upon inserting a killer event
  SELECT killer INTO vdead FROM event_name WHERE NEW.code=event_name.code;
  IF vdead THEN
    UPDATE matter SET dead = 1 WHERE matter.id=NEW.matter_id;
  END IF;

  -- Ensure that we are not in a nested trigger before updating the matter change time (happens for the Created event, which is inserted upon a matter creation)
  IF NEW.code != 'CRE' THEN
	UPDATE matter SET updated = Now(), updater = SUBSTRING_INDEX(USER(),'@',1) WHERE matter.id=NEW.matter_id;
  END IF;

END trig");

        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `event_before_update` BEFORE UPDATE ON `event` FOR EACH ROW BEGIN
	DECLARE vdate DATE DEFAULT NULL;

	SET new.updater=SUBSTRING_INDEX(USER(),'@',1);
	-- Date taken from Filed event in linked matter
	IF NEW.alt_matter_id IS NOT NULL THEN
		SELECT event_date INTO vdate FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id;
		SET NEW.event_date = vdate;
	END IF;
END");

        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `event_after_update` AFTER UPDATE ON `event` FOR EACH ROW trig: BEGIN

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

    UPDATE task set due_date=vdue_date WHERE id=vtask_id;

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

  UPDATE matter SET updated = Now(), updater = SUBSTRING_INDEX(USER(),'@',1) WHERE matter.id=NEW.matter_id;

END trig");

        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `event_after_delete` AFTER DELETE ON `event` FOR EACH ROW BEGIN
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

	UPDATE matter SET updated = Now(), updater = SUBSTRING_INDEX(USER(),'@',1) WHERE matter.id=OLD.matter_id;
END");
        // For event_name
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `ename_before_insert` BEFORE INSERT ON `event_name` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `ename_before_update` BEFORE UPDATE ON `event_name` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `ename_after_update` AFTER UPDATE ON `event_name` FOR EACH ROW BEGIN

	IF IFNULL(NEW.default_responsible,0) != IFNULL(OLD.default_responsible,0) THEN
		UPDATE task SET assigned_to=NEW.default_responsible
		WHERE code=NEW.code AND assigned_to <=> OLD.default_responsible;
	END IF;
END");
        // For matter
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `matter_before_insert` BEFORE INSERT ON `matter` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `matter_after_insert` AFTER INSERT ON `matter` FOR EACH ROW BEGIN
	DECLARE vactorid, vshared INT DEFAULT NULL;
	DECLARE vrole CHAR(5) DEFAULT NULL;


	INSERT INTO event (code, matter_id, event_date) VALUES ('CRE', NEW.id, now());


	SELECT actor_id, role, shared INTO vactorid, vrole, vshared FROM default_actor
	WHERE for_client IS NULL
	AND (for_country=NEW.country OR (for_country IS null AND NOT EXISTS (SELECT 1 FROM default_actor da WHERE da.for_country=NEW.country AND for_category=NEW.category_code)))
	AND for_category=NEW.category_code;


	IF (vactorid is NOT NULL AND (vshared=0 OR (vshared=1 AND NEW.container_id IS NULL))) THEN
		INSERT INTO matter_actor_lnk (matter_id, actor_id, role, shared) VALUES (NEW.id, vactorid, vrole, vshared);
	END IF;
END");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `matter_before_update` BEFORE UPDATE ON `matter` FOR EACH ROW BEGIN

set new.updater=SUBSTRING_INDEX(USER(),'@',1);


IF NEW.term_adjust != OLD.term_adjust THEN
	SET NEW.expire_date = OLD.expire_date + INTERVAL (NEW.term_adjust - OLD.term_adjust) DAY;
END IF;

END");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `matter_after_update` AFTER UPDATE ON `matter` FOR EACH ROW BEGIN

IF NEW.responsible != OLD.responsible THEN
	UPDATE task JOIN event ON (task.trigger_id=event.id AND event.matter_id=NEW.id) SET task.assigned_to=NEW.responsible
	WHERE task.done=0 AND task.assigned_to=OLD.responsible;
END IF;

END");
        // For matter_actor_lnk
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `malnk_before_insert` BEFORE INSERT ON `matter_actor_lnk` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `malnk_after_insert` AFTER INSERT ON `matter_actor_lnk` FOR EACH ROW BEGIN
	DECLARE vcli_ann_agt INT DEFAULT NULL;

	-- Delete renewal tasks when the special actor 'CLIENT' is set as the annuity agent
	IF NEW.role='ANN' THEN
		SELECT id INTO vcli_ann_agt FROM actor WHERE display_name='CLIENT';
		IF NEW.actor_id=vcli_ann_agt THEN
			DELETE task FROM event INNER JOIN task ON task.trigger_id=event.id
			WHERE task.code='REN' AND event.matter_id=NEW.matter_id;
		END IF;
	END IF;

	-- Check that we are not in a nested trigger before updating the matter change time
	IF (SELECT count(1) FROM default_actor WHERE NEW.actor_id = actor_id) = 0 THEN
		UPDATE matter SET updated = Now(), updater = SUBSTRING_INDEX(USER(),'@',1) WHERE matter.id=NEW.matter_id;
	END IF;

END");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `malnk_before_update` BEFORE UPDATE ON `matter_actor_lnk` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1)" );
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `matter_actor_lnk_AFTER_UPDATE` AFTER UPDATE ON `matter_actor_lnk` FOR EACH ROW
BEGIN

	UPDATE matter SET updated = Now(), updater = SUBSTRING_INDEX(USER(),'@',1) WHERE matter.id=NEW.matter_id;

END");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `matter_actor_lnk_AFTER_DELETE` AFTER DELETE ON `matter_actor_lnk` FOR EACH ROW
BEGIN

	UPDATE matter SET updated = Now(), updater = SUBSTRING_INDEX(USER(),'@',1) WHERE matter.id=OLD.matter_id;

END");
        // For matter_category
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `mcateg_creator_log` BEFORE INSERT ON `matter_category` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `mcateg_updater_log` BEFORE UPDATE ON `matter_category` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `mtype_creator_log` BEFORE INSERT ON `matter_type` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `mtype_updater_log` BEFORE UPDATE ON `matter_type` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1)");
        // For tasks
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `task_before_insert` BEFORE INSERT ON task FOR EACH ROW

BEGIN

	DECLARE vflag BOOLEAN;
	DECLARE vresp CHAR(16);

	SET NEW.creator = SUBSTRING_INDEX(USER(),'@',1);

	SELECT use_matter_resp INTO vflag FROM event_name WHERE event_name.code=NEW.code;
	SELECT responsible INTO vresp FROM matter, event WHERE event.id=NEW.trigger_id AND matter.id=event.matter_id;

	IF NEW.assigned_to IS NULL THEN
		IF vflag = 0 THEN
			SET NEW.assigned_to = (SELECT default_responsible FROM event_name WHERE event_name.code=NEW.code);
		ELSE
			SET NEW.assigned_to = (SELECT ifnull(default_responsible, vresp) FROM event_name WHERE event_name.code=NEW.code);
		END IF;
	ELSEIF NEW.assigned_to = '0' THEN
		SET NEW.assigned_to = vresp;
	END IF;

END");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `task_before_update` BEFORE UPDATE ON `task` FOR EACH ROW
BEGIN
	SET NEW.updater=SUBSTRING_INDEX(USER(),'@',1);
	IF NEW.done_date IS NOT NULL AND OLD.done_date IS NULL AND OLD.done = 0 THEN
		SET NEW.done = 1;
	END IF;
	IF NEW.done_date IS NULL AND OLD.done_date IS NOT NULL AND OLD.done = 1 THEN
		SET NEW.done = 0;
	END IF;
	IF NEW.done = 1 AND OLD.done = 0 AND OLD.done_date IS NULL THEN
		SET NEW.done_date = Least(OLD.due_date, Now());
	END IF;
	IF NEW.done = 0 AND OLD.done = 1 AND OLD.done_date IS NOT NULL THEN
		SET NEW.done_date = NULL;
	END IF;
	/*IF NEW.due_date != OLD.due_date AND old.rule_used IS NOT NULL THEN
		SET NEW.rule_used = NULL;
	END IF;*/
END");
        // For task_rules
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `trules_before_insert` BEFORE INSERT ON `task_rules` FOR EACH ROW set new.creator=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `trules_before_update` BEFORE UPDATE ON `task_rules` FOR EACH ROW set new.updater=SUBSTRING_INDEX(USER(),'@',1)");
        DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `trules_after_update` AFTER UPDATE ON `task_rules` FOR EACH ROW BEGIN
	IF (NEW.fee != OLD.fee OR NEW.cost != OLD.cost) THEN
		UPDATE task SET fee=NEW.fee, cost=NEW.cost WHERE rule_used=NEW.id AND done=0;
	END IF;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // For actor
        DB::unprepared("DROP TRIGGER IF EXISTS `actor_creator_log`");
        DB::unprepared("DROP TRIGGER IF EXISTS `actor_updater_log`");
        // For actor_role
        DB::unprepared("DROP TRIGGER IF EXISTS `arole_create_log`");
        DB::unprepared("DROP TRIGGER IF EXISTS `arole_modify_log`");
        // For classifier
        DB::unprepared("DROP TRIGGER IF EXISTS `classifier_BEFORE_INSERT`");
        DB::unprepared( "DROP TRIGGER IF EXISTS `classifier_updater_log`");
        // For classifier_type
        DB::unprepared("DROP TRIGGER IF EXISTS `ctype_creator_log`");
        DB::unprepared("DROP TRIGGER IF EXISTS `ctype_updater_log`");
        // For classifier value
        DB::unprepared("DROP TRIGGER IF EXISTS `cvalue_creator_log`");
        DB::unprepared("DROP TRIGGER IF EXISTS `cvalue_updater_log`");
        // For event
        DB::unprepared("DROP TRIGGER IF EXISTS `event_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `event_after_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `event_after_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `event_after_delete`");
        // For event_name
        DB::unprepared("DROP TRIGGER IF EXISTS `ename_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `ename_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `ename_after_update`");
        // For matter
        DB::unprepared("DROP TRIGGER IF EXISTS `matter_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `matter_after_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `matter_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `matter_after_update`");
        // For matter_actor_lnk
        DB::unprepared("DROP TRIGGER IF EXISTS `malnk_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `malnk_after_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `malnk_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `matter_actor_lnk_AFTER_DELETE`");
        // For matter_category
        DB::unprepared("DROP TRIGGER IF EXISTS `mcateg_creator_log`");
        DB::unprepared("DROP TRIGGER IF EXISTS `mcateg_updater_log`");
        DB::unprepared("DROP TRIGGER IF EXISTS `mtype_creator_log`");
        DB::unprepared("DROP TRIGGER IF EXISTS `mtype_updater_log`");
        // For tasks
        DB::unprepared("DROP TRIGGER IF EXISTS `task_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `task_before_update`");
        // For task_rules
        DB::unprepared("DROP TRIGGER IF EXISTS `trules_before_insert`");
        DB::unprepared("DROP TRIGGER IF EXISTS `trules_before_update`");
        DB::unprepared("DROP TRIGGER IF EXISTS `trules_after_update`");
    }
}
