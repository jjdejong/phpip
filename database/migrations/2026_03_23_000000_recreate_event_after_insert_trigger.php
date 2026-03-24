<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Recreate the event_after_insert trigger, which may have been dropped
     * during the utf8mb4_0900_ai_ci collation migration if the database user
     * lacked the SUPER / log_bin_trust_function_creators privilege at that time.
     * Without this trigger, rules do not fire automatically when events are
     * created; tasks only appear after a manual "Regenerate Tasks" action.
     */
    public function up(): void
    {
        // MySQL requires log_bin_trust_function_creators (or SUPER) to create
        // triggers when binary logging is enabled. SET GLOBAL must go through
        // exec() rather than prepare()/execute() to avoid a COM_STMT_PREPARE
        // protocol error.
        //
        // IMPORTANT: we only DROP the existing trigger after confirming we can
        // set the global variable (i.e. we have the privilege to recreate it).
        // If binary logging is off, the trigger can be created without the
        // variable — in that case $privilegeGranted stays false but we still
        // attempt the CREATE (skipping the DROP so nothing is lost on failure).
        $privilegeGranted = false;
        try {
            DB::getPdo()->exec('SET GLOBAL log_bin_trust_function_creators = 1');
            $privilegeGranted = true;
        } catch (\Exception $e) {
            echo "[WARNING] Could not set log_bin_trust_function_creators=1: " . $e->getMessage() . "\n"
                . "  Attempting to create the trigger without it (works when binary logging is disabled).\n"
                . "  If creation fails below, ask a DBA to run:\n"
                . "    SET GLOBAL log_bin_trust_function_creators = 1;\n"
                . "  then roll back and re-run this migration.\n";
        }

        // Only drop the trigger once we know we can recreate it.
        if ($privilegeGranted) {
            DB::statement('DROP TRIGGER IF EXISTS `event_after_insert`');
        }

        try {
            DB::unprepared("
                CREATE TRIGGER `event_after_insert` AFTER INSERT ON `event` FOR EACH ROW trig: BEGIN
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
                            -- Recurring renewal is the only possibility here, normally
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
                END trig
            ");

            echo "Recreated trigger: event_after_insert\n";
        } catch (\Exception $e) {
            if ($privilegeGranted) {
                echo "[ERROR] The event_after_insert trigger was dropped but could not be recreated: " . $e->getMessage() . "\n"
                    . "  Rules will NOT fire automatically when events are created until this is fixed.\n"
                    . "  To fix manually, ask a DBA to run the CREATE TRIGGER statement\n"
                    . "  found in database/schema/mysql-schema.sql (around line 282).\n";
            } else {
                echo "[ERROR] Could not create the event_after_insert trigger (binary logging is enabled and privilege was denied): " . $e->getMessage() . "\n"
                    . "  The existing trigger (if any) was NOT dropped.\n"
                    . "  To fix: ask a DBA to run:\n"
                    . "    SET GLOBAL log_bin_trust_function_creators = 1;\n"
                    . "  then roll back and re-run this migration:\n"
                    . "    php artisan migrate:rollback --step=1 && php artisan migrate\n";
            }
            throw $e;
        }
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS `event_after_insert`');
    }
};
