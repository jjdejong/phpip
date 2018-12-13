<?php

use Illuminate\Database\Migrations\Migration;

class CreateView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE OR REPLACE VIEW xxx_view AS SELECT name FROM users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW xxx_view');
    }
}
