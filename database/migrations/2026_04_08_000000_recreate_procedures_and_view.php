<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Recreate stored procedures and the event_lnk_list view with utf8mb4_0900_ai_ci collation.
     *
     * The 2025_10_17 collation migration converted all table columns to utf8mb4_0900_ai_ci but
     * skipped recreating stored procedures (incorrectly gating them behind the same
     * log_bin_trust_function_creators flag used for triggers).  As a result, procedure CHAR
     * parameters were left with utf8mb4_unicode_ci (frozen at creation time).  Comparing those
     * parameters against table columns — both IMPLICIT but with different collations — causes
     * MySQL error 1267: "Illegal mix of collations".
     *
     * Unlike triggers, stored procedures do NOT require SUPER or log_bin_trust_function_creators.
     * This migration runs without any DBA intervention.
     */
    public function up(): void
    {
        // Set the connection collation so that newly created objects inherit utf8mb4_0900_ai_ci.
        DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci');

        // Recreate the view (CREATE OR REPLACE — no DROP needed, no privilege issues).
        try {
            DB::statement("
                CREATE OR REPLACE VIEW `event_lnk_list` AS
                select `event`.`id` AS `id`,
                       `event`.`code` AS `code`,
                       `event`.`matter_id` AS `matter_id`,
                       if((`event`.`alt_matter_id` is null), `event`.`event_date`, `lnk`.`event_date`) AS `event_date`,
                       if((`event`.`alt_matter_id` is null), `event`.`detail`, `lnk`.`detail`) AS `detail`,
                       `matter`.`country` AS `country`
                from ((`event`
                    left join `event` `lnk`
                        on ((`event`.`alt_matter_id` = `lnk`.`matter_id`) and (`lnk`.`code` = 'FIL')))
                    left join `matter`
                        on (`event`.`alt_matter_id` = `matter`.`id`))
            ");
            echo "Recreated view: event_lnk_list\n";
        } catch (\Exception $e) {
            echo "[WARNING] Could not recreate view `event_lnk_list`: " . $e->getMessage() . "\n";
        }

        // Stored procedures to recreate.  Keys are procedure names; values are the CREATE bodies.
        // No DEFINER clause — uses the current DB user.
        $procedures = [

            'insert_recurring_renewals' => "
                CREATE PROCEDURE `insert_recurring_renewals`(
                    IN P_trigger_id INT,
                    IN P_rule_id INT,
                    IN P_base_date DATE,
                    IN P_responsible CHAR(16),
                    IN P_user CHAR(16)
                )
                proc: BEGIN
                    DECLARE FirstRenewal, RYear INT;
                    DECLARE BaseDate, StartDate, DueDate, ExpiryDate DATE DEFAULT NULL;
                    DECLARE Origin CHAR(2) DEFAULT NULL;

                    SELECT ebase.event_date, estart.event_date, country.renewal_first, matter.expire_date, matter.origin
                        INTO BaseDate, StartDate, FirstRenewal, ExpiryDate, Origin
                        FROM country
                        JOIN matter ON country.iso = matter.country
                        JOIN event estart ON estart.matter_id = matter.id AND estart.id = P_trigger_id
                        JOIN event ebase ON ebase.matter_id = matter.id
                        WHERE country.renewal_start = estart.code
                        AND country.renewal_base = ebase.code;

                    -- Leave if the country has no parameters (country dealt with specifically in task_rules)
                    IF StartDate IS NULL THEN
                        LEAVE proc;
                    END IF;
                    SET BaseDate = LEAST(BaseDate, P_base_date);
                    SET RYear = ABS(FirstRenewal);
                    renloop: WHILE RYear <= 20 DO
                        IF (FirstRenewal > 0) THEN
                            SET DueDate = BaseDate + INTERVAL RYear - 1 YEAR;
                        ELSE
                            SET DueDate = StartDate + INTERVAL RYear - 1 YEAR;
                        END IF;
                        IF DueDate > ExpiryDate THEN
                            LEAVE proc;
                        END IF;
                        IF DueDate < StartDate THEN
                            SET DueDate = StartDate;
                        END IF;
                        -- Ignore renewals in the past beyond the 6-months grace period unless PCT national phase
                        IF (DueDate < Now() - INTERVAL 6 MONTH AND Origin != 'WO') OR (DueDate < (Now() - INTERVAL 19 MONTH) AND Origin = 'WO') THEN
                            SET RYear = RYear + 1;
                            ITERATE renloop;
                        END IF;
                        INSERT INTO task (trigger_id, code, due_date, detail, rule_used, assigned_to, creator, created_at, updated_at)
                        VALUES (P_trigger_id, 'REN', DueDate, RYear, P_rule_id, P_responsible, P_user, Now(), Now());
                        SET RYear = RYear + 1;
                    END WHILE;
                END proc
            ",

            'recalculate_tasks' => "
                CREATE PROCEDURE `recalculate_tasks`(
                    IN P_matter_id INT,
                    IN P_event_code CHAR(5),
                    IN P_user CHAR(16)
                )
                proc: BEGIN
                    DECLARE e_event_date, DueDate, BaseDate DATE DEFAULT NULL;
                    DECLARE t_id, e_id, tr_days, tr_months, tr_years, tr_recurring, m_pta INT DEFAULT NULL;
                    DECLARE Done, tr_end_of_month, tr_use_priority BOOLEAN DEFAULT 0;
                    DECLARE m_category, m_country, m_type CHAR(5) DEFAULT NULL;

                    DECLARE cur_rule CURSOR FOR
                        SELECT task.id, days, months, years, recurring, end_of_month, use_priority
                        FROM task_rules
                        JOIN task ON task.rule_used = task_rules.id
                        WHERE task.trigger_id = e_id;

                    DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

                    SELECT id, event_date INTO e_id, e_event_date FROM event_lnk_list WHERE matter_id = P_matter_id AND code = P_event_code ORDER BY event_date LIMIT 1;

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
                            ITERATE update_tasks;
                        END IF;

                        SET BaseDate = e_event_date;

                        IF tr_use_priority THEN
                            SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = P_matter_id;
                        END IF;

                        SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
                        IF tr_end_of_month THEN
                            SET DueDate = LAST_DAY(DueDate);
                        END IF;

                        UPDATE task SET due_date = DueDate, updated_at = Now(), updater = P_user WHERE task.id = t_id;
                    END LOOP update_tasks;
                    CLOSE cur_rule;

                    IF P_event_code = 'FIL' THEN
                        SELECT category_code, term_adjust, country, type_code INTO m_category, m_pta, m_country, m_type FROM matter WHERE matter.id = P_matter_id;
                        SELECT months, years INTO tr_months, tr_years FROM task_rules
                            WHERE task = 'EXP'
                            AND for_category = m_category
                            AND IF (for_country IS NOT NULL AND for_type IS NULL,
                                for_country = m_country,
                                concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category) = (m_country, m_category))
                            ) AND IF (for_type IS NOT NULL AND for_country IS NULL,
                                for_type = m_type,
                                concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category) = (m_type, m_category))
                            );
                        SET DueDate = BaseDate + INTERVAL m_pta DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
                        UPDATE matter SET expire_date = DueDate WHERE matter.id = P_matter_id;
                    END IF;
                END proc
            ",

            'recreate_tasks' => "
                CREATE PROCEDURE `recreate_tasks`(
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

                    SELECT e.matter_id, e.event_date, e.code, m.country, m.type_code, m.dead, m.expire_date, m.term_adjust, m.origin, m.parent_id
                        INTO e_matter_id, e_event_date, e_code, m_country, m_type_code, m_dead, m_expiry, m_pta, m_origin, m_parent_id
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
                        -- Recurring renewal is the only possibility here, normally
                            CALL insert_recurring_renewals(P_trigger_id, tr_id, BaseDate, tr_responsible, P_user);
                        END IF;
                    END LOOP create_tasks;
                    CLOSE cur_rule;
                    SET Done = 0;

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
                END proc
            ",

            'update_expired' => "
                CREATE PROCEDURE `update_expired`()
                BEGIN
                    DECLARE vmatter_id INTEGER;
                    DECLARE vexpire_date DATE;
                    DECLARE done INT DEFAULT FALSE;
                    DECLARE cur_expired CURSOR FOR
                        SELECT matter.id, matter.expire_date FROM matter WHERE expire_date < Now() AND dead=0;
                    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

                    OPEN cur_expired;

                    read_loop: LOOP
                        FETCH cur_expired INTO vmatter_id, vexpire_date;
                        IF done THEN
                            LEAVE read_loop;
                        END IF;
                        INSERT IGNORE INTO `event` (code, matter_id, event_date, created_at, creator, updated_at) VALUES ('EXP', vmatter_id, vexpire_date, Now(), 'system', Now());
                    END LOOP;
                END
            ",
        ];

        foreach ($procedures as $name => $sql) {
            try {
                DB::unprepared("DROP PROCEDURE IF EXISTS `{$name}`");
                DB::unprepared(trim($sql));
                echo "Recreated procedure: {$name}\n";
            } catch (\Exception $e) {
                echo "[WARNING] Could not recreate procedure `{$name}`: " . $e->getMessage() . "\n";
            }
        }
    }

    public function down(): void
    {
        foreach (['insert_recurring_renewals', 'recalculate_tasks', 'recreate_tasks', 'update_expired'] as $name) {
            DB::unprepared("DROP PROCEDURE IF EXISTS `{$name}`");
        }
    }
};
