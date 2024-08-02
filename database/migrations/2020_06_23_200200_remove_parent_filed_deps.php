<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_insert`');
        DB::unprepared("CREATE TRIGGER `event_after_insert` AFTER INSERT ON `event` FOR EACH ROW trig: BEGIN
	DECLARE DueDate, BaseDate, m_expiry DATE DEFAULT NULL;
	DECLARE tr_id, tr_days, tr_months, tr_years, m_pta, lnk_matter_id, CliAnnAgt, m_parent_id INT DEFAULT NULL;
	DECLARE tr_task, m_type_code, tr_currency, m_country, m_origin CHAR(5) DEFAULT NULL;
	DECLARE tr_detail, tr_responsible VARCHAR(160) DEFAULT NULL;
	DECLARE Done, tr_clear_task, tr_delete_task, tr_end_of_month, tr_recurring, tr_use_priority, m_dead BOOLEAN DEFAULT 0;
	DECLARE tr_cost, tr_fee DECIMAL(6,2) DEFAULT null;

	DECLARE cur_rule CURSOR FOR
		SELECT task_rules.id, task, clear_task, delete_task, detail, days, months, years, recurring, end_of_month, use_priority, cost, fee, currency, task_rules.responsible
		FROM task_rules
		JOIN event_name ON event_name.code = task_rules.task
		JOIN matter ON matter.id = NEW.matter_id
		WHERE task_rules.active = 1
		AND task_rules.for_category = matter.category_code
		AND NEW.code = task_rules.trigger_event
		AND (Now() < use_before OR use_before IS null)
		AND (Now() > use_after OR use_after IS null)
		AND IF (task_rules.for_country IS NOT NULL,
			task_rules.for_country = matter.country,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category, tr.active) = (matter.country, matter.category_code, 1))
		)
		AND IF (task_rules.for_origin IS NOT NULL,
			task_rules.for_origin = matter.origin,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_origin, tr.for_category, tr.active) = (matter.origin, matter.category_code, 1))
		)
		AND IF (task_rules.for_type IS NOT NULL,
			task_rules.for_type = matter.type_code,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category, tr.active) = (matter.type_code, matter.category_code, 1))
		)
		AND NOT EXISTS (SELECT 1 FROM event WHERE event.matter_id = NEW.matter_id AND event.code = task_rules.abort_on)
		AND IF (task_rules.condition_event IS NULL, true, EXISTS (SELECT 1 FROM event WHERE event.matter_id = NEW.matter_id AND event.code = task_rules.condition_event));

	DECLARE cur_linked CURSOR FOR
		SELECT matter_id FROM event WHERE event.alt_matter_id = NEW.matter_id;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	SELECT type_code, dead, expire_date, term_adjust, country, origin, parent_id INTO m_type_code, m_dead, m_expiry, m_pta, m_country, m_origin, m_parent_id FROM matter WHERE matter.id = NEW.matter_id;
	SELECT id INTO CliAnnAgt FROM actor WHERE display_name = 'CLIENT';

	-- Do not change anything in dead cases
	IF (m_dead) THEN
		LEAVE trig;
	END IF;

	OPEN cur_rule;
		create_tasks: LOOP
			SET BaseDate = NEW.event_date;
			FETCH cur_rule INTO tr_id, tr_task, tr_clear_task, tr_delete_task, tr_detail, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_priority, tr_cost, tr_fee, tr_currency, tr_responsible;

			IF Done THEN
				LEAVE create_tasks;
			END IF;

			-- Skip renewal tasks if the client is the annuity agent
			IF tr_task = 'REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role = 'ANN' AND lnk.actor_id = CliAnnAgt AND lnk.matter_id = NEW.matter_id) THEN
				ITERATE create_tasks;
			END IF;

			-- Skip recurring renewal tasks if country table has no correspondence
			IF tr_task = 'REN' AND tr_recurring = 1 AND NOT EXISTS (SELECT 1 FROM country WHERE iso = m_country and renewal_start = NEW.code) THEN
				ITERATE create_tasks;
			END IF;

			-- Use earliest priority date for task deadline calculation, if a PRI event exists
			IF tr_use_priority THEN
				SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = NEW.matter_id;
			END IF;

			-- Clear the task identified by the rule (do not create anything)
			IF tr_clear_task THEN
				UPDATE task JOIN event ON task.trigger_id = event.id
					SET task.done_date = NEW.event_date
					WHERE task.code = tr_task AND event.matter_id = NEW.matter_id AND task.done = 0;
				ITERATE create_tasks;
			END IF;

			-- Delete the task identified by the rule (do not create anything)
			IF tr_delete_task THEN
				DELETE FROM task
					WHERE task.code = tr_task AND task.trigger_id IN (SELECT event.id FROM event WHERE event.matter_id = NEW.matter_id);
				ITERATE create_tasks;
			END IF;

			-- Calculate the deadline
			SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
			IF tr_end_of_month THEN
				SET DueDate = LAST_DAY(DueDate);
			END IF;

			-- Deadline for renewals that have become due in a parent case when filing a divisional (EP 4-months rule)
			IF tr_task = 'REN' AND m_parent_id IS NOT NULL AND DueDate < NEW.event_date THEN
				SET DueDate = NEW.event_date + INTERVAL 4 MONTH;
			END IF;

			-- Don't create tasks having a deadline in the past, except for expiry and renewal
			-- Create renewals up to 6 months in the past in general, create renewals up to 19 months in the past for PCT national phases (captures direct PCT filing situations)
			IF (DueDate < Now() AND tr_task NOT IN ('EXP', 'REN'))
			OR (DueDate < (Now() - INTERVAL 6 MONTH) AND tr_task = 'REN' AND m_origin != 'WO')
			OR (DueDate < (Now() - INTERVAL 19 MONTH) AND tr_task = 'REN' AND m_origin = 'WO')
			THEN
				ITERATE create_tasks;
			END IF;

			IF tr_task = 'EXP' THEN
			-- Handle the expiry task (no task created)
				UPDATE matter SET expire_date = DueDate + INTERVAL m_pta DAY WHERE matter.id = NEW.matter_id;
			ELSEIF tr_recurring = 0 THEN
			-- Insert the new task if it is not a recurring one
				INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
				VALUES (NEW.id, tr_task, DueDate, tr_detail, tr_id, tr_cost, tr_fee, tr_currency, tr_responsible, NEW.creator, Now(), Now());
			ELSEIF tr_task = 'REN' THEN
			-- Recurring renewal is the only possibilty here, normally
				CALL insert_recurring_renewals(NEW.id, tr_id, BaseDate, tr_responsible, NEW.creator);
			END IF;
		END LOOP create_tasks;
	CLOSE cur_rule;
	SET Done = 0;

	-- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
	IF NEW.code = 'FIL' THEN
		OPEN cur_linked;
			recalc_linked: LOOP
				FETCH cur_linked INTO lnk_matter_id;
				IF Done THEN
					LEAVE recalc_linked;
				END IF;
				CALL recalculate_tasks(lnk_matter_id, 'FIL', NEW.creator);
			END LOOP recalc_linked;
		CLOSE cur_linked;
	END IF;

	-- Recalculate tasks based on the priority date upon inserting a new priority claim
	IF NEW.code = 'PRI' THEN
		CALL recalculate_tasks(NEW.matter_id, 'FIL', NEW.creator);
	END IF;

	-- Set matter to dead upon inserting a killer event
	SELECT killer INTO m_dead FROM event_name WHERE NEW.code = event_name.code;
	IF m_dead THEN
		UPDATE matter SET dead = 1 WHERE matter.id = NEW.matter_id;
	END IF;
END trig");

        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_update`');
        DB::unprepared("CREATE TRIGGER `event_after_update` AFTER UPDATE ON `event` FOR EACH ROW trig: BEGIN
  DECLARE DueDate, BaseDate DATE DEFAULT NULL;
  DECLARE t_id, tr_days, tr_months, tr_years, tr_recurring, m_pta, lnk_matter_id INT DEFAULT NULL;
  DECLARE Done, tr_end_of_month, tr_use_priority BOOLEAN DEFAULT 0;
  DECLARE m_category, m_country CHAR(5) DEFAULT NULL;

	DECLARE cur_rule CURSOR FOR
		SELECT task.id, days, months, years, recurring, end_of_month, use_priority
		FROM task_rules
		JOIN task ON task.rule_used = task_rules.id
		WHERE task.trigger_id = NEW.id;

  DECLARE cur_linked CURSOR FOR
  	SELECT matter_id FROM event WHERE alt_matter_id = NEW.matter_id;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	-- Leave if date hasn't changed
	IF (OLD.event_date = NEW.event_date AND NEW.alt_matter_id <=> OLD.alt_matter_id) THEN
		LEAVE trig;
	END IF;

  SET BaseDate = NEW.event_date;

  OPEN cur_rule;
  update_tasks: LOOP
  	FETCH cur_rule INTO t_id, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_priority;

  	IF Done THEN
  	  LEAVE update_tasks;
  	END IF;

		IF tr_recurring = 1 THEN
			-- Recurring tasks are not managed
			ITERATE update_tasks;
		END IF;

    -- Use earliest priority date for task deadline calculation, if a PRI event exists
  	IF tr_use_priority THEN
  	  SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = NEW.matter_id;
  	END IF;

    -- Calculate the deadline
  	SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
  	IF tr_end_of_month THEN
  	  SET DueDate = LAST_DAY(DueDate);
  	END IF;

		UPDATE task set due_date = DueDate, updated_at = Now(), updater = NEW.updater WHERE id = t_id;
  END LOOP update_tasks;
  CLOSE cur_rule;
  SET Done = 0;

  -- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
  IF NEW.code = 'FIL' THEN
  	OPEN cur_linked;
  	recalc_linked: LOOP
  	  FETCH cur_linked INTO lnk_matter_id;
  	  IF Done THEN
  		  LEAVE recalc_linked;
  	  END IF;
  	  CALL recalculate_tasks(lnk_matter_id, 'FIL', NEW.updater);
  	  CALL recalculate_tasks(lnk_matter_id, 'PRI', NEW.updater);
  	END LOOP recalc_linked;
  	CLOSE cur_linked;
  END IF;

  -- Recalculate tasks based on the priority date upon inserting a new priority claim
  IF NEW.code = 'PRI' THEN
  	CALL recalculate_tasks(NEW.matter_id, 'FIL', NEW.updater);
  END IF;

	-- Recalculate expiry
	IF NEW.code = 'FIL' THEN
  	SELECT category_code, term_adjust, country INTO m_category, m_pta, m_country FROM matter WHERE matter.id = NEW.matter_id;
  	SELECT months, years INTO tr_months, tr_years FROM task_rules
  		WHERE task = 'EXP'
  		AND for_category = m_category
			AND IF (for_country IS NOT NULL,
				for_country = m_country,
				concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category) = (m_country, m_category))
			);
		SELECT IFNULL(min(event_date), NEW.event_date) INTO BaseDate FROM event_lnk_list WHERE code = 'FIL' AND matter_id = NEW.matter_id;
  	SET DueDate = BaseDate + INTERVAL m_pta DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
  	UPDATE matter SET expire_date = DueDate WHERE matter.id = NEW.matter_id;
  END IF;
END trig");

        DB::unprepared('DROP PROCEDURE IF EXISTS `recalculate_tasks`');
        DB::unprepared("CREATE PROCEDURE `recalculate_tasks`(
			IN P_matter_id INT,
			IN P_event_code CHAR(5),
			IN P_user CHAR(16)
		)
proc: BEGIN
	DECLARE e_event_date, DueDate, BaseDate DATE DEFAULT NULL;
	DECLARE t_id, e_id, tr_days, tr_months, tr_years, tr_recurring, m_pta INT DEFAULT NULL;
	DECLARE Done, tr_end_of_month, tr_use_priority BOOLEAN DEFAULT 0;
	DECLARE m_category, m_country CHAR(5) DEFAULT NULL;

	DECLARE cur_rule CURSOR FOR
		SELECT task.id, days, months, years, recurring, end_of_month, use_priority
		FROM task_rules
		JOIN task ON task.rule_used = task_rules.id
		WHERE task.trigger_id = e_id;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	SELECT id, event_date INTO e_id, e_event_date FROM event_lnk_list WHERE matter_id = P_matter_id AND code = P_event_code ORDER BY event_date LIMIT 1;
	-- Leave if matter does not have the required event
	IF e_id IS NULL THEN
		LEAVE proc;
	END IF;

	OPEN cur_rule;
	update_tasks: LOOP
		FETCH cur_rule INTO t_id, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_priority;

		IF Done THEN
			LEAVE update_tasks;
		END IF;

		IF tr_recurring = 1 THEN
			-- Recurring tasks are not managed
			ITERATE update_tasks;
		END IF;

    SET BaseDate = e_event_date;

		-- Use earliest priority date for task deadline calculation, if a PRI event exists
		IF tr_use_priority THEN
			SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = P_matter_id;
		END IF;

		-- Calculate the deadline
		SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
		IF tr_end_of_month THEN
			SET DueDate = LAST_DAY(DueDate);
		END IF;

		UPDATE task SET due_date = DueDate, updated_at = Now(), updater = P_user WHERE task.id = t_id;
	END LOOP update_tasks;
	CLOSE cur_rule;

	-- Recalculate expiry
	IF P_event_code = 'FIL' THEN
		SELECT category_code, term_adjust, country INTO m_category, m_pta, m_country FROM matter WHERE matter.id = P_matter_id;
		SELECT months, years INTO tr_months, tr_years FROM task_rules
			WHERE task = 'EXP'
			AND for_category = m_category
			AND IF (for_country IS NOT NULL,
				for_country = m_country,
				concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category) = (m_country, m_category))
			);
		SET DueDate = BaseDate + INTERVAL m_pta DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
		UPDATE matter SET expire_date = DueDate WHERE matter.id = P_matter_id;
	END IF;
END proc");

        DB::unprepared('DROP PROCEDURE IF EXISTS `recreate_tasks`');
        DB::unprepared("CREATE PROCEDURE `recreate_tasks`(
			IN P_trigger_id INT,
			IN P_user CHAR(16)
		)
proc: BEGIN
	DECLARE e_event_date, DueDate, BaseDate, m_expiry DATE DEFAULT NULL;
	DECLARE e_matter_id, tr_id, tr_days, tr_months, tr_years, m_pta, lnk_matter_id, CliAnnAgt, m_parent_id INT DEFAULT NULL;
	DECLARE e_code, tr_task, m_country, m_type_code, tr_currency, m_origin CHAR(5) DEFAULT NULL;
	DECLARE tr_detail, tr_responsible VARCHAR(160) DEFAULT NULL;
	DECLARE Done, tr_clear_task, tr_delete_task, tr_end_of_month, tr_recurring, tr_use_priority, m_dead BOOLEAN DEFAULT 0;
	DECLARE tr_cost, tr_fee DECIMAL(6,2) DEFAULT null;

	DECLARE cur_rule CURSOR FOR
		SELECT task_rules.id, task, clear_task, delete_task, detail, days, months, years, recurring, end_of_month, use_priority, cost, fee, currency, task_rules.responsible
		FROM task_rules
		JOIN event_name ON event_name.code = task_rules.task
		JOIN matter ON matter.id = e_matter_id
		WHERE task_rules.active = 1
		AND task_rules.for_category = matter.category_code
		AND e_code = task_rules.trigger_event
		AND (Now() < use_before OR use_before IS null)
		AND (Now() > use_after OR use_after IS null)
		AND IF (task_rules.for_country IS NOT NULL,
			task_rules.for_country = matter.country,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category, tr.active) = (matter.country, matter.category_code, 1))
		)
		AND IF (task_rules.for_origin IS NOT NULL,
			task_rules.for_origin = matter.origin,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_origin, tr.for_category, tr.active) = (matter.origin, matter.category_code, 1))
		)
		AND IF (task_rules.for_type IS NOT NULL,
			task_rules.for_type = matter.type_code,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category, tr.active) = (matter.type_code, matter.category_code, 1))
		)
		AND NOT EXISTS (SELECT 1 FROM event WHERE event.matter_id = e_matter_id AND event.code = task_rules.abort_on)
		AND IF (task_rules.condition_event IS NULL, true, EXISTS (SELECT 1 FROM event WHERE matter_id = e_matter_id AND event.code = task_rules.condition_event));

	DECLARE cur_linked CURSOR FOR
		SELECT matter_id FROM event WHERE event.alt_matter_id = e_matter_id;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	-- Delete the tasks attached to the event by an existing rule
	DELETE FROM task WHERE rule_used IS NOT NULL AND trigger_id = P_trigger_id;

  SELECT e.matter_id, e.event_date, e.code, m.country, m.type_code, m.dead, m.expire_date, m.term_adjust, m.origin, m.parent_id INTO e_matter_id, e_event_date, e_code, m_country, m_type_code, m_dead, m_expiry, m_pta, m_origin, m_parent_id
	FROM event e
	JOIN matter m ON m.id = e.matter_id
	WHERE e.id = P_trigger_id;

  SELECT id INTO CliAnnAgt FROM actor WHERE display_name = 'CLIENT';

	-- Do not change anything in dead cases
	IF (m_dead OR Now() > m_expiry) THEN
    LEAVE proc;
  END IF;

	OPEN cur_rule;
	  create_tasks: LOOP
			SET BaseDate = e_event_date;
	    FETCH cur_rule INTO tr_id, tr_task, tr_clear_task, tr_delete_task, tr_detail, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_priority, tr_cost, tr_fee, tr_currency, tr_responsible;

			IF Done THEN
	      LEAVE create_tasks;
	    END IF;

			-- Skip renewal tasks if the client is the annuity agent
			IF tr_task = 'REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role = 'ANN' AND lnk.actor_id = CliAnnAgt AND lnk.matter_id = e_matter_id) THEN
				ITERATE create_tasks;
			END IF;

			-- Skip recurring renewal tasks if country table has no correspondence
			IF tr_task = 'REN' AND tr_recurring = 1 AND NOT EXISTS (SELECT 1 FROM country WHERE iso = m_country and renewal_start = e_code) THEN
				ITERATE create_tasks;
			END IF;

			-- Use earliest priority date for task deadline calculation, if a PRI event exists
			IF tr_use_priority THEN
				SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = e_matter_id;
			END IF;

			-- Clear the task identified by the rule (do not create anything)
	    IF tr_clear_task THEN
	      UPDATE task JOIN event ON task.trigger_id = event.id
					SET task.done_date = e_event_date
					WHERE task.code = tr_task AND event.matter_id = e_matter_id AND task.done = 0;
	      ITERATE create_tasks;
	    END IF;

			-- Delete the task identified by the rule (do not create anything)
	    IF tr_delete_task THEN
				DELETE FROM task
					WHERE task.code = tr_task AND task.trigger_id IN (SELECT event.id FROM event WHERE event.matter_id = e_matter_id);
				ITERATE create_tasks;
	    END IF;

			-- Calculate the deadline
			SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
	    IF tr_end_of_month THEN
	      SET DueDate = LAST_DAY(DueDate);
	    END IF;


			-- Deadline for renewals that have become due in a parent case when filing a divisional (EP 4-months rule)
			IF tr_task = 'REN' AND m_parent_id IS NOT NULL AND DueDate < e_event_date THEN
				SET DueDate = e_event_date + INTERVAL 4 MONTH;
			END IF;

			-- Don't create tasks having a deadline in the past, except for expiry and renewal
      -- Create renewals up to 6 months in the past in general, create renewals up to 19 months in the past for PCT national phases (captures direct PCT filing situations)
			IF (DueDate < Now() AND tr_task NOT IN ('EXP', 'REN'))
			OR (DueDate < (Now() - INTERVAL 6 MONTH) AND tr_task = 'REN' AND m_origin != 'WO')
			OR (DueDate < (Now() - INTERVAL 19 MONTH) AND tr_task = 'REN' AND m_origin = 'WO')
			THEN
	      ITERATE create_tasks;
	    END IF;

	    IF tr_task = 'EXP' THEN
			-- Handle the expiry task (no task created)
				UPDATE matter SET expire_date = DueDate + INTERVAL m_pta DAY WHERE matter.id = e_matter_id;
			ELSEIF tr_recurring = 0 THEN
			-- Insert the new task if it is not a recurring one
				INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
		    VALUES (P_trigger_id, tr_task, DueDate, tr_detail, tr_id, tr_cost, tr_fee, tr_currency, tr_responsible, P_user, Now(), Now());
			ELSEIF tr_task = 'REN' THEN
			-- Recurring renewal is the only possibilty here, normally
				CALL insert_recurring_renewals(P_trigger_id, tr_id, BaseDate, tr_responsible, P_user);
			END IF;
	  END LOOP create_tasks;
  CLOSE cur_rule;
  SET done = 0;

	-- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
	IF e_code = 'FIL' THEN
		OPEN cur_linked;
			recalc_linked: LOOP
				FETCH cur_linked INTO lnk_matter_id;
				IF Done THEN
					LEAVE recalc_linked;
				END IF;
				CALL recalculate_tasks(lnk_matter_id, 'FIL', P_user);
			END LOOP recalc_linked;
		CLOSE cur_linked;
  END IF;

	-- Recalculate tasks based on the priority date upon inserting a new priority claim
  IF e_code = 'PRI' THEN
    CALL recalculate_tasks(e_matter_id, 'FIL', P_user);
  END IF;

	-- Set matter to dead upon inserting a killer event
	SELECT killer INTO m_dead FROM event_name WHERE e_code = event_name.code;
  IF m_dead THEN
    UPDATE matter SET dead = 1 WHERE matter.id = e_matter_id;
  END IF;
END proc");

        DB::table('task_rules')->where([['for_country', 'BR'], ['task', 'EXP'], ['for_category', 'PAT']])->delete();
        DB::unprepared("create temporary table pfilings like event;
insert into pfilings (id, code, matter_id, event_date)
select id, code, matter_id, min(event_date) from event
where code = 'PFIL'
group by matter_id, code
order by matter_id");
        DB::unprepared("create temporary table filings like event;
insert into filings
select event.* from event join pfilings using (matter_id)
where event.code = 'FIL'");
        DB::unprepared("update event join filings using (matter_id)
set event.event_date = filings.event_date, event.detail = 'Child filing date'
where event.code = 'ENT'");
        DB::unprepared("insert into event (matter_id, code, event_date, detail)
select filings.matter_id, 'ENT', filings.event_date, 'Child filing date'
from filings
where not exists (select 1 from event ent where ent.matter_id = filings.matter_id and ent.code = 'ENT')");
        DB::unprepared("update event join pfilings using (matter_id)
set event.event_date = pfilings.event_date
where event.code = 'FIL'
and event.event_date != pfilings.event_date");
        DB::unprepared("delete from event where code = 'PFIL'");
        DB::unprepared("insert ignore into event (code, matter_id, alt_matter_id)
select 'PFIL', matter.id, matter.parent_id
from matter join matter parent on parent.id = matter.parent_id
where matter.origin in ('EP', 'WO')
and parent.country = matter.origin");
        DB::table('event_name')->where('code', 'ENT')->update(['status_event' => 1, 'notes' => 'Actual filing date of a child matter']);

        /* Doesnt run
        $matters = App\Models\Matter::has('parentFiling')->has('filing')->with('parentFiling', 'filing', 'entered');
        foreach ($matters as $matter) {
          $matter->entered()->updateOrCreate(
            ['code' => 'ENT'],
            ['event_date' => $matter->filing->event_date,
            'detail' => 'Child filing date']
          );

          $matter->filing->event_date = $matter->parentFiling()->min('event_date');
          $matter->filing->save();
        }*/

    }

    public function down()
    {
        //
    }
};
