<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTables extends Migration
{
    protected $tables = [
      'actor',
      'actor_role',
      'classifier',
      'classifier_type',
      'classifier_value',
      'event',
      'event_name',
      'matter',
      'matter_actor_lnk',
      'matter_category',
      'matter_type',
      'task',
      'task_rules'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      foreach ($this->tables as $t) {
        if (Schema::hasColumn($t, 'updated')) {
          Schema::table($t, function (Blueprint $table) use ($t) {
            $table->dateTime('created_at')->after('creator')->nullable();
            $table->dateTime('updated')->nullable()->change();
            $table->renameColumn('updated', 'updated_at');
            if ($t == 'actor_role') {
              if (Schema::hasColumn($t, 'box')) {
                $table->dropColumn(['box', 'box_color']);
              }
        			$table->boolean('shareable')->default(0)->comment('Indicates whether actors with this role are shareable by default with all matters of the same family')->change();
        			$table->boolean('show_ref')->default(0)->change();
        			$table->boolean('show_company')->default(0)->change();
        			$table->boolean('show_rate')->default(0)->change();
        			$table->boolean('show_date')->default(0)->change();
            }
            if ($t == 'actor') {
              $table->boolean('phy_person')->default(1)->comment('Physical person or not')->change();
              $table->boolean('small_entity')->default(0)->comment('Small entity status used in a few countries (FR, US)')->change();
              $table->string('address', 256)->comment('Main address: street, zip and city')->nullable()->change();
              $table->string('address_mailing', 256)->comment('Mailing address: street, zip and city')->nullable()->change();
              $table->string('address_billing', 256)->comment('Billing address: street, zip and city')->nullable()->change();
              $table->boolean('warn')->default(0)->comment('The actor will be displayed in red in the matter view when set')->change();
            }
            if ($t == 'matter') {
              $table->boolean('dead')->default(0)->comment('Indicates that the case is no longer supervised. Automatically set by "killer events" like "Abandoned"')->change();
            }
          });
        }
      }

      DB::unprepared("DROP TRIGGER IF EXISTS `event_after_insert`");
      DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `event_after_insert` AFTER INSERT ON `event` FOR EACH ROW
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
        END trig"
      );

      DB::unprepared("DROP TRIGGER IF EXISTS `event_after_update`");
      DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `event_after_update` AFTER UPDATE ON `event` FOR EACH ROW
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
        END trig"
      );

      DB::unprepared("DROP TRIGGER IF EXISTS `event_after_delete`");
      DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `event_after_delete` AFTER DELETE ON `event` FOR EACH ROW
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
        END"
      );

      DB::unprepared("DROP TRIGGER IF EXISTS `malnk_after_insert`");
      DB::unprepared("CREATE DEFINER = `root`@`localhost` TRIGGER `malnk_after_insert` AFTER INSERT ON `matter_actor_lnk` FOR EACH ROW
        BEGIN
          DECLARE vcli_ann_agt INT DEFAULT NULL;

          -- Delete renewal tasks when the special actor 'CLIENT' is set as the annuity agent
          IF NEW.role='ANN' THEN
            SELECT id INTO vcli_ann_agt FROM actor WHERE display_name='CLIENT';
            IF NEW.actor_id=vcli_ann_agt THEN
              DELETE task FROM event INNER JOIN task ON task.trigger_id=event.id
              WHERE task.code='REN' AND event.matter_id=NEW.matter_id;
            END IF;
          END IF;
        END"
      );

      DB::unprepared("DROP TRIGGER IF EXISTS `matter_actor_lnk_AFTER_UPDATE`");

      DB::unprepared("DROP TRIGGER IF EXISTS `matter_actor_lnk_AFTER_DELETE`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      foreach ($this->tables as $t) {
        if (Schema::hasColumn($t, 'updated_at')) {
          Schema::table($t, function (Blueprint $table) {
            $table->renameColumn('updated_at', 'updated');
            $table->dropColumn('created_at');
          });
        }
      }
    }
}
