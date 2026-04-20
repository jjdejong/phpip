<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure task.detail JSON writes are valid in trigger/procedure paths.
     */
    public function up(): void
    {
        DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci');

        DB::statement('DROP PROCEDURE IF EXISTS `insert_recurring_renewals`');
        DB::unprepared(<<<'SQL'
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

                    IF (DueDate < Now() - INTERVAL 6 MONTH AND Origin != 'WO')
                    OR (DueDate < (Now() - INTERVAL 19 MONTH) AND Origin = 'WO') THEN
                        SET RYear = RYear + 1;
                        ITERATE renloop;
                    END IF;

                    BEGIN
                        DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
                        INSERT INTO task (trigger_id, code, due_date, detail, rule_used, assigned_to, creator, created_at, updated_at)
                        VALUES (
                            P_trigger_id,
                            'REN',
                            DueDate,
                            JSON_OBJECT('en', CAST(RYear AS CHAR)),
                            P_rule_id,
                            P_responsible,
                            P_user,
                            Now(),
                            Now()
                        );
                    END;

                    SET RYear = RYear + 1;
                END WHILE;
            END proc
        SQL);

        DB::statement('DROP TRIGGER IF EXISTS `event_after_insert`');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER `event_after_insert` AFTER INSERT ON `event` FOR EACH ROW trig: BEGIN
                DECLARE DueDate, BaseDate, m_expiry DATE DEFAULT NULL;
                DECLARE tr_id, tr_days, tr_months, tr_years, m_pta, lnk_matter_id, CliAnnAgt, m_parent_id INT DEFAULT NULL;
                DECLARE tr_task CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;
                DECLARE m_type_code CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;
                DECLARE tr_currency CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;
                DECLARE m_country CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;
                DECLARE m_origin CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;
                DECLARE tr_detail VARCHAR(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;
                DECLARE tr_responsible CHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;
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
                        task_rules.for_country COLLATE utf8mb4_0900_ai_ci = matter.country COLLATE utf8mb4_0900_ai_ci,
                        concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category, tr.active) = (matter.country, matter.category_code, 1))
                    )
                    AND IF (task_rules.for_origin IS NOT NULL,
                        task_rules.for_origin COLLATE utf8mb4_0900_ai_ci = matter.origin COLLATE utf8mb4_0900_ai_ci,
                        concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_origin, tr.for_category, tr.active) = (matter.origin, matter.category_code, 1))
                    )
                    AND IF (task_rules.for_type IS NOT NULL,
                        task_rules.for_type COLLATE utf8mb4_0900_ai_ci = matter.type_code COLLATE utf8mb4_0900_ai_ci,
                        concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category, tr.active) = (matter.type_code, matter.category_code, 1))
                    )
                    AND NOT EXISTS (SELECT 1 FROM event WHERE event.matter_id = NEW.matter_id AND event.code = task_rules.abort_on)
                    AND IF (task_rules.condition_event IS NULL, true, EXISTS (SELECT 1 FROM event WHERE event.matter_id = NEW.matter_id AND event.code = task_rules.condition_event));

                DECLARE cur_linked CURSOR FOR
                    SELECT matter_id FROM event WHERE event.alt_matter_id = NEW.matter_id;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

                SELECT type_code, dead, expire_date, term_adjust, country, origin, parent_id INTO m_type_code, m_dead, m_expiry, m_pta, m_country, m_origin, m_parent_id FROM matter WHERE matter.id = NEW.matter_id;
                SELECT id INTO CliAnnAgt FROM actor WHERE display_name = 'CLIENT';

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

                        IF tr_task = 'REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role = 'ANN' AND lnk.actor_id = CliAnnAgt AND lnk.matter_id = NEW.matter_id) THEN
                            ITERATE create_tasks;
                        END IF;

                        IF tr_task = 'REN' AND tr_recurring = 1 AND NOT EXISTS (SELECT 1 FROM country WHERE iso COLLATE utf8mb4_0900_ai_ci = m_country COLLATE utf8mb4_0900_ai_ci and renewal_start = NEW.code) THEN
                            ITERATE create_tasks;
                        END IF;

                        IF tr_use_priority THEN
                            SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = NEW.matter_id;
                        END IF;

                        IF tr_clear_task THEN
                            UPDATE task JOIN event ON task.trigger_id = event.id
                                SET task.done_date = NEW.event_date
                                WHERE task.code COLLATE utf8mb4_0900_ai_ci = tr_task COLLATE utf8mb4_0900_ai_ci AND event.matter_id = NEW.matter_id AND task.done = 0;
                            ITERATE create_tasks;
                        END IF;

                        IF tr_delete_task THEN
                            DELETE FROM task
                                WHERE task.code COLLATE utf8mb4_0900_ai_ci = tr_task COLLATE utf8mb4_0900_ai_ci
                                  AND task.trigger_id IN (SELECT event.id FROM event WHERE event.matter_id = NEW.matter_id);
                            ITERATE create_tasks;
                        END IF;

                        SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
                        IF tr_end_of_month THEN
                            SET DueDate = LAST_DAY(DueDate);
                        END IF;

                        IF tr_task = 'REN' AND m_parent_id IS NOT NULL AND DueDate < NEW.event_date THEN
                            SET DueDate = NEW.event_date + INTERVAL 4 MONTH;
                        END IF;

                        IF (DueDate < Now() AND tr_task NOT IN ('EXP', 'REN'))
                        OR (DueDate < (Now() - INTERVAL 6 MONTH) AND tr_task = 'REN' AND m_origin != 'WO')
                        OR (DueDate < (Now() - INTERVAL 19 MONTH) AND tr_task = 'REN' AND m_origin = 'WO')
                        THEN
                            ITERATE create_tasks;
                        END IF;

                        IF tr_task = 'EXP' THEN
                            UPDATE matter SET expire_date = DueDate + INTERVAL m_pta DAY WHERE matter.id = NEW.matter_id;
                        ELSEIF tr_recurring = 0 THEN
                            BEGIN
                                DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
                                INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
                                VALUES (
                                    NEW.id,
                                    tr_task,
                                    DueDate,
                                    CASE
                                        WHEN tr_detail IS NULL THEN NULL
                                        WHEN JSON_VALID(tr_detail) AND JSON_TYPE(CAST(tr_detail AS JSON)) = 'OBJECT' THEN CAST(tr_detail AS JSON)
                                        ELSE JSON_OBJECT('en', tr_detail)
                                    END,
                                    tr_id,
                                    tr_cost,
                                    tr_fee,
                                    tr_currency,
                                    tr_responsible,
                                    NEW.creator,
                                    Now(),
                                    Now()
                                );
                            END;
                        ELSEIF tr_task = 'REN' THEN
                            CALL insert_recurring_renewals(NEW.id, tr_id, BaseDate, tr_responsible, NEW.creator);
                        END IF;
                    END LOOP create_tasks;
                CLOSE cur_rule;
                SET Done = 0;

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

                IF NEW.code = 'PRI' THEN
                    CALL recalculate_tasks(NEW.matter_id, 'FIL', NEW.creator);
                END IF;

                SELECT killer INTO m_dead FROM event_name WHERE NEW.code = event_name.code;
                IF m_dead THEN
                    UPDATE matter SET dead = 1 WHERE matter.id = NEW.matter_id;
                END IF;
            END trig
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS `event_after_insert`');
        DB::statement('DROP PROCEDURE IF EXISTS `insert_recurring_renewals`');
    }
};
