<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Matters lost their expiry date, and renewals were then generated past the patent term, when:
     * - a filing event was deleted, even though another filing event remained (e.g. a duplicate);
     * - recalculate_tasks ran on a matter whose filing event has no tasks (e.g. upon adding a priority
     *   claim to a national phase), because the expiry was computed from an unset base date.
     *
     * 1. Fix the expiry calculation of recalculate_tasks.
     * 2. Recreate event_after_delete so that the expiry is recomputed from a remaining filing event.
     * 3. Restore the missing expiry dates of live patents having a filing event.
     * 4. Delete the untouched renewals of these patents that fall after the restored expiry date.
     */
    public function up(): void
    {
        DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci');

        // 1. Procedure
        DB::unprepared('DROP PROCEDURE IF EXISTS `recalculate_tasks`');
        DB::unprepared(<<<'SQL'
            CREATE PROCEDURE `recalculate_tasks`(
                IN P_matter_id INT,
                IN P_event_code CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
                IN P_user CHAR(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci
            )
            proc: BEGIN
                DECLARE e_event_date, DueDate, BaseDate DATE DEFAULT NULL;
                DECLARE t_id, e_id, tr_days, tr_months, tr_years, tr_recurring, m_pta INT DEFAULT NULL;
                DECLARE Done, tr_end_of_month, tr_use_priority BOOLEAN DEFAULT 0;
                DECLARE m_category CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;
                DECLARE m_country CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;
                DECLARE m_type CHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL;

                DECLARE cur_rule CURSOR FOR
                    SELECT task.id, days, months, years, recurring, end_of_month, use_priority
                    FROM task_rules
                    JOIN task ON task.rule_used = task_rules.id
                    WHERE task.trigger_id = e_id;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

                SELECT id, event_date INTO e_id, e_event_date
                FROM event_lnk_list
                WHERE matter_id = P_matter_id
                  AND code COLLATE utf8mb4_0900_ai_ci = P_event_code COLLATE utf8mb4_0900_ai_ci
                ORDER BY event_date
                LIMIT 1;

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
                        SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate
                        FROM event_lnk_list
                        WHERE code = 'PRI' AND matter_id = P_matter_id;
                    END IF;

                    SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
                    IF tr_end_of_month THEN
                        SET DueDate = LAST_DAY(DueDate);
                    END IF;

                    UPDATE task SET due_date = DueDate, updated_at = Now(), updater = P_user WHERE task.id = t_id;
                END LOOP update_tasks;
                CLOSE cur_rule;

                IF P_event_code = 'FIL' THEN
                    SELECT category_code, term_adjust, country, type_code
                    INTO m_category, m_pta, m_country, m_type
                    FROM matter
                    WHERE matter.id = P_matter_id;

                    -- Do not inherit the values of the last task processed above
                    SET tr_months = NULL, tr_years = NULL, tr_use_priority = 0;

                    SELECT months, years, use_priority INTO tr_months, tr_years, tr_use_priority
                    FROM task_rules
                    WHERE task = 'EXP'
                      AND for_category COLLATE utf8mb4_0900_ai_ci = m_category COLLATE utf8mb4_0900_ai_ci
                      AND IF (for_country IS NOT NULL AND for_type IS NULL,
                          for_country COLLATE utf8mb4_0900_ai_ci = m_country COLLATE utf8mb4_0900_ai_ci,
                          concat(task, trigger_event) NOT IN (
                              SELECT concat(task, trigger_event)
                              FROM task_rules tr
                              WHERE (tr.for_country, tr.for_category) = (m_country, m_category)
                          )
                      )
                      AND IF (for_type IS NOT NULL AND for_country IS NULL,
                          for_type COLLATE utf8mb4_0900_ai_ci = m_type COLLATE utf8mb4_0900_ai_ci,
                          concat(task, trigger_event) NOT IN (
                              SELECT concat(task, trigger_event)
                              FROM task_rules tr
                              WHERE (tr.for_type, tr.for_category) = (m_type, m_category)
                          )
                      );

                    -- The expiry counts from the filing date, not from the base date of the last task processed,
                    -- which is unset when the filing event has no tasks
                    IF tr_years IS NOT NULL THEN
                        SET BaseDate = e_event_date;
                        IF tr_use_priority THEN
                            SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate
                            FROM event_lnk_list
                            WHERE code = 'PRI' AND matter_id = P_matter_id;
                        END IF;

                        SET DueDate = BaseDate + INTERVAL m_pta DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
                        UPDATE matter SET expire_date = DueDate WHERE matter.id = P_matter_id;
                    END IF;
                END IF;
            END proc
        SQL);

        // 2. Trigger
        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_delete`');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER `event_after_delete` AFTER DELETE ON `event` FOR EACH ROW BEGIN
                IF OLD.code IN ('PRI','PFIL') THEN
                    CALL recalculate_tasks(OLD.matter_id, 'FIL', OLD.updater);
                END IF;
                IF OLD.code = 'FIL' THEN
                    -- Recompute the expiry (and the filing based tasks) from a remaining filing event, if any
                    IF EXISTS (SELECT 1 FROM event WHERE event.matter_id = OLD.matter_id AND event.code = 'FIL') THEN
                        CALL recalculate_tasks(OLD.matter_id, 'FIL', OLD.updater);
                    ELSE
                        UPDATE matter SET expire_date = NULL WHERE matter.id = OLD.matter_id;
                    END IF;
                END IF;
                UPDATE matter
                    JOIN event_name ON (OLD.code = event_name.code)
                    SET matter.dead = 0
                    WHERE matter.id = OLD.matter_id
                    AND NOT EXISTS (SELECT 1 FROM event JOIN event_name en ON (event.code = en.code) WHERE event.matter_id = OLD.matter_id AND en.killer = 1);
            END
        SQL);

        // 3. Missing expiry dates, computed as in the recalculate_tasks procedure
        $matters = DB::select(
            "SELECT m.id, m.uid, m.category_code, m.country, m.type_code, m.term_adjust,
                (SELECT MIN(l.event_date) FROM event_lnk_list l WHERE l.matter_id = m.id AND l.code = 'FIL') AS fil_date,
                (SELECT MIN(l.event_date) FROM event_lnk_list l WHERE l.matter_id = m.id AND l.code = 'PRI') AS pri_date
            FROM matter m
            WHERE m.dead = 0 AND m.category_code = 'PAT' AND m.expire_date IS NULL
            AND EXISTS (SELECT 1 FROM event e WHERE e.matter_id = m.id AND e.code = 'FIL')"
        );

        $fixed = [];
        foreach ($matters as $matter) {
            $rules = DB::select(
                "SELECT months, years, use_priority FROM task_rules
                WHERE task = 'EXP'
                AND for_category = ?
                AND IF (for_country IS NOT NULL AND for_type IS NULL,
                    for_country = ?,
                    concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category) = (?, ?)))
                AND IF (for_type IS NOT NULL AND for_country IS NULL,
                    for_type = ?,
                    concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category) = (?, ?)))",
                [$matter->category_code, $matter->country, $matter->country, $matter->category_code,
                    $matter->type_code, $matter->type_code, $matter->category_code]
            );

            if (count($rules) != 1 || !$matter->fil_date) {
                echo "Expiry not restored for {$matter->uid}: " . count($rules) . " expiry rules, filing date " . ($matter->fil_date ?? 'none') . "\n";
                continue;
            }

            $baseDate = $rules[0]->use_priority && $matter->pri_date ? $matter->pri_date : $matter->fil_date;
            DB::update(
                'UPDATE matter SET expire_date = ? + INTERVAL ? DAY + INTERVAL ? MONTH + INTERVAL ? YEAR, updated_at = Now(), updater = ?
                WHERE id = ? AND expire_date IS NULL',
                [$baseDate, $matter->term_adjust, $rules[0]->months, $rules[0]->years, 'system', $matter->id]
            );
            $fixed[] = $matter->id;
        }
        echo 'Expiry restored for ' . count($fixed) . " patents\n";

        // 4. Renewals past the restored expiry, only if nobody has acted on them yet
        if ($fixed) {
            $deleted = DB::delete(
                "DELETE task FROM task
                JOIN event ON event.id = task.trigger_id
                JOIN matter ON matter.id = event.matter_id
                WHERE matter.id IN (" . implode(',', array_fill(0, count($fixed), '?')) . ")
                AND task.code = 'REN'
                AND task.done = 0
                AND IFNULL(task.step, 0) = 0
                AND IFNULL(task.invoice_step, 0) = 0
                AND task.due_date > matter.expire_date",
                $fixed
            );
            echo "Deleted $deleted renewals due after the restored expiry dates\n";
        }
    }

    /**
     * Restores the former procedure and trigger. The restored expiry dates and deleted renewals are not reverted.
     */
    public function down(): void
    {
        (require database_path('migrations/2026_04_09_000003_force_recreate_recalculate_tasks_with_explicit_collation.php'))->up();

        DB::statement('SET NAMES utf8mb4 COLLATE utf8mb4_0900_ai_ci');

        DB::unprepared('DROP TRIGGER IF EXISTS `event_after_delete`');
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER `event_after_delete` AFTER DELETE ON `event` FOR EACH ROW BEGIN
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
            END
        SQL);
    }
};
