<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `actor_creator_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `actor_updater_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `arole_create_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `arole_modify_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `classifier_BEFORE_INSERT`');
        DB::unprepared('DROP TRIGGER IF EXISTS `classifier_updater_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ctype_creator_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ctype_updater_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `cvalue_creator_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `cvalue_updater_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `event_before_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `event_before_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ename_before_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `ename_before_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `matter_before_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `matter_before_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `malnk_before_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `malnk_before_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `mcateg_creator_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `mcateg_updater_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `mtype_creator_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `mtype_updater_log`');
        DB::unprepared('DROP TRIGGER IF EXISTS `task_before_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `task_before_update`');
        DB::unprepared('DROP TRIGGER IF EXISTS `trules_before_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `trules_before_update`');

        DB::unprepared("CREATE TRIGGER `classifier_before_insert` BEFORE INSERT ON `classifier` FOR EACH ROW
  BEGIN
    IF NEW.type_code = 'TITEN' THEN
  		SET NEW.value=tcase(NEW.value);
  	ELSEIF NEW.type_code IN ('TIT', 'TITOF', 'TITAL') THEN
  		SET NEW.value=CONCAT(UCASE(SUBSTR(NEW.value, 1, 1)),LCASE(SUBSTR(NEW.value FROM 2)));
  	END IF;
  END"
        );

        DB::unprepared("CREATE TRIGGER `event_before_insert` BEFORE INSERT ON `event` FOR EACH ROW
  BEGIN
    DECLARE vdate DATE DEFAULT NULL;
  	IF NEW.alt_matter_id IS NOT NULL THEN
  		IF EXISTS (SELECT 1 FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id AND event_date IS NOT NULL) THEN
  			SELECT event_date INTO vdate FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id;
  			SET NEW.event_date = vdate;
  		ELSE
  			SET NEW.event_date = Now();
  		END IF;
  	END IF;
  END"
        );

        DB::unprepared("CREATE TRIGGER `event_before_update` BEFORE UPDATE ON `event` FOR EACH ROW
  BEGIN
  	DECLARE vdate DATE DEFAULT NULL;
  	-- Date taken from Filed event in linked matter
  	IF NEW.alt_matter_id IS NOT NULL THEN
  		SELECT event_date INTO vdate FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id;
  		SET NEW.event_date = vdate;
  	END IF;
  END"
        );

        DB::unprepared('CREATE TRIGGER `matter_before_update` BEFORE UPDATE ON `matter` FOR EACH ROW
  BEGIN
  	IF NEW.term_adjust != OLD.term_adjust THEN
  		SET NEW.expire_date = OLD.expire_date + INTERVAL (NEW.term_adjust - OLD.term_adjust) DAY;
  	END IF;
  END'
        );

        DB::unprepared("CREATE TRIGGER `task_before_insert` BEFORE INSERT ON task FOR EACH ROW
  BEGIN
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
  END"
        );

        DB::unprepared('CREATE TRIGGER `task_before_update` BEFORE UPDATE ON `task` FOR EACH ROW
  BEGIN
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
  END'
        );
    }

    public function down()
    {
        // Doesn't matter - creator and updater logs were not compatible with Laravel
    }
};
