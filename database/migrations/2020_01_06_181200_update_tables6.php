<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
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
        Schema::table('matter', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('matter');
            if (array_key_exists('uid', $indexes)) {
                $table->dropIndex('uid');
            }
            $table->unique('uid', 'uid_uq');
        });

        // For matter_actor_lnk
        DB::unprepared('DROP TRIGGER IF EXISTS `matter_actor_lnk_AFTER_UPDATE`');
        DB::unprepared("CREATE TRIGGER `matter_actor_lnk_AFTER_UPDATE` AFTER UPDATE ON `matter_actor_lnk` FOR EACH ROW
BEGIN
  DECLARE vcli_ann_agt INT DEFAULT NULL;

  -- Delete renewal tasks when the special actor 'CLIENT' is set as the annuity agent
  IF NEW.role = 'ANN' THEN
  	SELECT id INTO vcli_ann_agt FROM actor WHERE display_name = 'CLIENT';
  	IF NEW.actor_id = vcli_ann_agt THEN
  	  DELETE task FROM event INNER JOIN task ON task.trigger_id = event.id
  	  WHERE task.code = 'REN' AND event.matter_id = NEW.matter_id;
  	END IF;
  END IF;
END"
        );

    }

    public function down()
    {
        Schema::table('matter', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $sm->listTableIndexes('matter');
            if (array_key_exists('uid_uq', $indexes)) {
                $table->dropUnique('uid_uq');
            }
            $table->index('uid', 'uid');
        });

        DB::unprepared('DROP TRIGGER IF EXISTS `matter_actor_lnk_AFTER_UPDATE`');
    }
};
