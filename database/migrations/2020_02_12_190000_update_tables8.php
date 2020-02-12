<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class UpdateTables8 extends Migration
{
    /**
     * Run the migrations.
     *
     * IMPORTANT this migration will generate unique key constraint violation errors if the uid values in the matter table are not unique.
     * If you get such errors, the matter will be identified in the error message and you then need to fix it and run the migration again.
     * Fix by adding a type and/or index to the matter.
     * Run the migration as many times as necessary until successful.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS `matter_after_insert`");
        DB::unprepared("CREATE TRIGGER `matter_after_insert` AFTER INSERT ON `matter` FOR EACH ROW
BEGIN
	DECLARE vactorid, vshared INT DEFAULT NULL;
	DECLARE vrole CHAR(5) DEFAULT NULL;
	INSERT INTO event (code, matter_id, event_date, created_at, creator, updated_at) VALUES ('CRE', NEW.id, Now(), NEW.creator, Now());
	SELECT actor_id, role, shared INTO vactorid, vrole, vshared FROM default_actor
		WHERE for_client IS NULL
		AND (for_country = NEW.country OR (for_country IS null AND NOT EXISTS (SELECT 1 FROM default_actor da WHERE da.for_country = NEW.country AND for_category = NEW.category_code)))
		AND for_category = NEW.category_code;
	IF (vactorid is NOT NULL AND (vshared = 0 OR (vshared = 1 AND NEW.container_id IS NULL))) THEN
		INSERT INTO matter_actor_lnk (matter_id, actor_id, role, shared, created_at, creator, updated_at) VALUES (NEW.id, vactorid, vrole, vshared, Now(), 'system', Now());
	END IF;
END");

        DB::unprepared("DROP TRIGGER IF EXISTS `matter_after_update`");
        DB::unprepared("CREATE TRIGGER `matter_after_update` AFTER UPDATE ON `matter` FOR EACH ROW
BEGIN
	IF NEW.responsible != OLD.responsible THEN
		UPDATE task JOIN event ON (task.trigger_id = event.id AND event.matter_id = NEW.id) SET task.assigned_to = NEW.responsible, updated_at = Now(), updater = NEW.updater
		WHERE task.done = 0 AND task.assigned_to = OLD.responsible;
	END IF;
END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
