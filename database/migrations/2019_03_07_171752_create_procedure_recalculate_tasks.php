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
        DB::unprepared("CREATE PROCEDURE `recalculate_tasks`(
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
END proc");
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
