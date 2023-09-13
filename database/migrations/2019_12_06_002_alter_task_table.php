<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('task', function (Blueprint $table) {
            $t = 'task';
            if ( !Schema::hasColumn($t, 'step')) {
                $table->tinyinteger('step')->after('currency')->default(0);
            }
            if ( !Schema::hasColumn($t, 'grace_period')) {
                $table->tinyinteger('grace_period')->after('step')->default(0);
            }
            if ( !Schema::hasColumn($t, 'invoice_step')) {
                $table->tinyinteger('invoice_step')->after('step')->default(0);
            }
        });

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
    SET NEW.done_date = NULL, NEW.step = 0, NEW.invoice_step = 0, NEW.grace_period = 0;
  END IF;
END");
    }
    public function down()
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

        $t = 'task';
        if (Schema::hasColumn($t, 'step')) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn('step');
            });
        }
        if (Schema::hasColumn($t, 'invoice_step')) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn('invoice_step');
            });
        }
        if (Schema::hasColumn($t, 'grace_period')) {
            Schema::table($t, function (Blueprint $table) {
                $table->dropColumn('grace_period');
            });
        }
    }
};
