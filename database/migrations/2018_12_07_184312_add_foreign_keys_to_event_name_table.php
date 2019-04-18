<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEventNameTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('event_name', function(Blueprint $table)
		{
			$table->foreign('default_responsible')->references('login')->on('actor')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('event_name', function(Blueprint $table)
		{
			$table->dropForeign(['default_responsible']);
		});
	}

}
