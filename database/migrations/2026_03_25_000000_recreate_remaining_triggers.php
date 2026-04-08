<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Recreate triggers that were dropped by the utf8mb4_0900_ai_ci collation migration
     * and not yet restored.  Only missing triggers are touched — existing ones are left
     * alone.  This makes the migration safe to run even when some triggers survived the
     * collation migration intact, and idempotent on re-run.
     *
     * event_after_insert is handled by the earlier migration and is skipped here.
     */
    public function up(): void
    {
        // Best-effort: try to set log_bin_trust_function_creators so that CREATE TRIGGER
        // works when binary logging is enabled.  We do NOT abort if this fails — we still
        // attempt the creates below (they will succeed when binary logging is off).
        try {
            DB::getPdo()->exec('SET GLOBAL log_bin_trust_function_creators = 1');
        } catch (\Exception $e) {
            echo "[WARNING] Could not set log_bin_trust_function_creators=1: " . $e->getMessage() . "\n"
                . "  Trigger creation will be attempted anyway; it will succeed when binary\n"
                . "  logging is disabled.  If creation fails below, ask a DBA to run:\n"
                . "    SET GLOBAL log_bin_trust_function_creators = 1;\n"
                . "  then re-run this migration.\n";
        }

        // Find which triggers already exist so we can skip them.
        $database = DB::connection()->getDatabaseName();
        $existing = DB::select(
            'SELECT TRIGGER_NAME FROM information_schema.TRIGGERS WHERE TRIGGER_SCHEMA = ?',
            [$database]
        );
        $existingNames = array_map(fn($r) => $r->TRIGGER_NAME, $existing);

        $triggers = [
            'classifier_before_insert' => "
                CREATE TRIGGER `classifier_before_insert` BEFORE INSERT ON `classifier` FOR EACH ROW BEGIN
                    IF NEW.type_code = 'TITEN' THEN
                        SET NEW.value=tcase(NEW.value);
                    ELSEIF NEW.type_code IN ('TIT', 'TITOF', 'TITAL') THEN
                        SET NEW.value=CONCAT(UCASE(SUBSTR(NEW.value, 1, 1)),LCASE(SUBSTR(NEW.value FROM 2)));
                    END IF;
                END
            ",

            'event_before_insert' => "
                CREATE TRIGGER `event_before_insert` BEFORE INSERT ON `event` FOR EACH ROW BEGIN
                    DECLARE vdate DATE DEFAULT NULL;
                    IF NEW.alt_matter_id IS NOT NULL THEN
                        IF EXISTS (SELECT 1 FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id AND event_date IS NOT NULL) THEN
                            SELECT event_date INTO vdate FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id;
                            SET NEW.event_date = vdate;
                        ELSE
                            SET NEW.event_date = Now();
                        END IF;
                    END IF;
                END
            ",

            'event_before_update' => "
                CREATE TRIGGER `event_before_update` BEFORE UPDATE ON `event` FOR EACH ROW BEGIN
                    DECLARE vdate DATE DEFAULT NULL;
                    -- Date taken from Filed event in linked matter
                    IF NEW.alt_matter_id IS NOT NULL THEN
                        SELECT event_date INTO vdate FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id;
                        SET NEW.event_date = vdate;
                    END IF;
                END
            ",

            'event_after_update' => "
                CREATE TRIGGER `event_after_update` AFTER UPDATE ON `event` FOR EACH ROW trig: BEGIN
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

                    -- If the event is a filing, update the tasks of the matters linked by event
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

                    -- Recalculate tasks based on the priority date upon updating a priority claim
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
                END trig
            ",

            'event_after_delete' => "
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
            ",

            'ename_after_update' => "
                CREATE TRIGGER `ename_after_update` AFTER UPDATE ON `event_name` FOR EACH ROW BEGIN
                    IF IFNULL(NEW.default_responsible,0) != IFNULL(OLD.default_responsible,0) THEN
                        UPDATE task SET assigned_to=NEW.default_responsible
                        WHERE code=NEW.code AND assigned_to <=> OLD.default_responsible;
                    END IF;
                END
            ",

            'matter_after_insert' => "
                CREATE TRIGGER `matter_after_insert` AFTER INSERT ON `matter` FOR EACH ROW BEGIN
                    DECLARE vactorid, vshared INT DEFAULT NULL;
                    DECLARE vrole CHAR(5) DEFAULT NULL;
                    INSERT INTO event (code, matter_id, event_date, created_at, creator, updated_at) VALUES ('CRE', NEW.id, Now(), Now(), NEW.creator, Now());
                    SELECT actor_id, role, shared INTO vactorid, vrole, vshared FROM default_actor
                        WHERE for_client IS NULL
                        AND (for_country = NEW.country OR (for_country IS null AND NOT EXISTS (SELECT 1 FROM default_actor da WHERE da.for_country = NEW.country AND for_category = NEW.category_code)))
                        AND for_category = NEW.category_code;
                    IF (vactorid is NOT NULL AND (vshared = 0 OR (vshared = 1 AND NEW.container_id IS NULL))) THEN
                        INSERT INTO matter_actor_lnk (matter_id, actor_id, role, shared, created_at, creator, updated_at) VALUES (NEW.id, vactorid, vrole, vshared, Now(), 'system', Now());
                    END IF;
                END
            ",

            'matter_before_update' => "
                CREATE TRIGGER `matter_before_update` BEFORE UPDATE ON `matter` FOR EACH ROW BEGIN
                    IF NEW.term_adjust != OLD.term_adjust THEN
                        SET NEW.expire_date = OLD.expire_date + INTERVAL (NEW.term_adjust - OLD.term_adjust) DAY;
                    END IF;
                END
            ",

            'matter_after_update' => "
                CREATE TRIGGER `matter_after_update` AFTER UPDATE ON `matter` FOR EACH ROW BEGIN
                    IF NEW.responsible != OLD.responsible THEN
                        UPDATE task JOIN event ON (task.trigger_id = event.id AND event.matter_id = NEW.id)
                            SET task.assigned_to = NEW.responsible, task.updated_at = Now(), task.updater = NEW.updater
                            WHERE task.done = 0 AND task.assigned_to = OLD.responsible;
                    END IF;
                END
            ",

            'malnk_after_insert' => "
                CREATE TRIGGER `malnk_after_insert` AFTER INSERT ON `matter_actor_lnk` FOR EACH ROW BEGIN
                    DECLARE vcli_ann_agt INT DEFAULT NULL;

                    -- Delete renewal tasks when the special actor 'CLIENT' is set as the annuity agent
                    IF NEW.role='ANN' THEN
                        SELECT id INTO vcli_ann_agt FROM actor WHERE display_name='CLIENT';
                        IF NEW.actor_id=vcli_ann_agt THEN
                            DELETE task FROM event INNER JOIN task ON task.trigger_id=event.id
                            WHERE task.code='REN' AND event.matter_id=NEW.matter_id;
                        END IF;
                    END IF;
                END
            ",

            'matter_actor_lnk_AFTER_UPDATE' => "
                CREATE TRIGGER `matter_actor_lnk_AFTER_UPDATE` AFTER UPDATE ON `matter_actor_lnk` FOR EACH ROW BEGIN
                    DECLARE vcli_ann_agt INT DEFAULT NULL;

                    -- Delete renewal tasks when the special actor 'CLIENT' is set as the annuity agent
                    IF NEW.role = 'ANN' THEN
                        SELECT id INTO vcli_ann_agt FROM actor WHERE display_name = 'CLIENT';
                        IF NEW.actor_id = vcli_ann_agt THEN
                            DELETE task FROM event INNER JOIN task ON task.trigger_id = event.id
                            WHERE task.code = 'REN' AND event.matter_id = NEW.matter_id;
                        END IF;
                    END IF;
                END
            ",

            'task_before_insert' => "
                CREATE TRIGGER `task_before_insert` BEFORE INSERT ON `task` FOR EACH ROW BEGIN
                    DECLARE vflag BOOLEAN;
                    DECLARE vresp CHAR(16);
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
                END
            ",

            'task_before_update' => "
                CREATE TRIGGER `task_before_update` BEFORE UPDATE ON `task` FOR EACH ROW BEGIN
                    IF NEW.done_date IS NOT NULL AND OLD.done_date IS NULL AND OLD.done = 0 THEN
                        SET NEW.done = 1;
                    END IF;
                    IF NEW.done_date IS NULL AND OLD.done_date IS NOT NULL AND OLD.done = 1 THEN
                        SET NEW.done = 0;
                    END IF;
                    IF NEW.done = 1 AND OLD.done = 0 AND NEW.done_date IS NULL THEN
                        SET NEW.done_date = Least(OLD.due_date, Now());
                    END IF;
                    IF NEW.done = 0 AND OLD.done = 1 AND OLD.done_date IS NOT NULL THEN
                        SET NEW.done_date = NULL, NEW.step = 0, NEW.invoice_step = 0, NEW.grace_period = 0;
                    END IF;
                END
            ",

            'trules_after_update' => "
                CREATE TRIGGER `trules_after_update` AFTER UPDATE ON `task_rules` FOR EACH ROW BEGIN
                    IF (NEW.fee != OLD.fee OR NEW.cost != OLD.cost) THEN
                        UPDATE task SET fee=NEW.fee, cost=NEW.cost WHERE rule_used=NEW.id AND done=0;
                    END IF;
                END
            ",
        ];

        $failed = [];

        foreach ($triggers as $name => $sql) {
            if (in_array($name, $existingNames)) {
                echo "Trigger `{$name}` already exists, skipping.\n";
                continue;
            }

            // Trigger is missing — create it (no DROP needed since it doesn't exist).
            try {
                DB::unprepared(trim($sql));
                echo "Recreated trigger: {$name}\n";
            } catch (\Exception $e) {
                echo "[ERROR] Could not create trigger `{$name}`: " . $e->getMessage() . "\n"
                    . "  To fix: ask a DBA to run:\n"
                    . "    SET GLOBAL log_bin_trust_function_creators = 1;\n"
                    . "  then re-run this migration.\n"
                    . "  Alternatively, create the trigger manually using the SQL in\n"
                    . "  database/schema/mysql-schema.sql.\n";
                $failed[] = $name;
            }
        }

        if (!empty($failed)) {
            throw new \RuntimeException(
                'The following triggers could not be created: ' . implode(', ', $failed) . '. '
                . 'See warnings above. Re-run the migration after granting the required privilege.'
            );
        }
    }

    public function down(): void
    {
        $triggers = [
            'classifier_before_insert',
            'event_before_insert',
            'event_before_update',
            'event_after_update',
            'event_after_delete',
            'ename_after_update',
            'matter_after_insert',
            'matter_before_update',
            'matter_after_update',
            'malnk_after_insert',
            'matter_actor_lnk_AFTER_UPDATE',
            'task_before_insert',
            'task_before_update',
            'trules_after_update',
        ];

        foreach ($triggers as $name) {
            DB::statement("DROP TRIGGER IF EXISTS `{$name}`");
        }
    }
};
