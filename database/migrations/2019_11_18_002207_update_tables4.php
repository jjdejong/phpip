<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTables4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::unprepared("DROP TRIGGER IF EXISTS `task_before_update`");
      DB::unprepared("CREATE TRIGGER `task_before_update` BEFORE UPDATE ON `task` FOR EACH ROW
BEGIN
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
    SET NEW.done_date = NULL;
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
        // Better not rollback this one
    }
}
