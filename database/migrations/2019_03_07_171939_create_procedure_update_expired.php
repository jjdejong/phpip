<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class UpdateExpired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(
"CREATE DEFINER=`root`@`localhost` PROCEDURE `update_expired`()
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
		INSERT IGNORE INTO `event` (code, matter_id, event_date) VALUES ('EXP', vmatter_id, vexpire_date);
	END LOOP;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS `update_expired`');
    }
}
