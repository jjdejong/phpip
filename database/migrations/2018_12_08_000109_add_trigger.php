<?php

use Illuminate\Database\Migrations\Migration;

class AddTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::unprepared('CREATE TRIGGER xxx_trigger AFTER INSERT ON `table` FOR EACH ROW
      BEGIN
        //
      END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::unprepared('DROP TRIGGER `xxx_trigger`');
    }
}
