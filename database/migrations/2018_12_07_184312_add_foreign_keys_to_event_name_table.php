<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {

	public function up()
	{
		Schema::table('event_name', function(Blueprint $table)
		{
			$table->foreign('default_responsible')->references('login')->on('actor')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	public function down()
	{
		Schema::table('event_name', function(Blueprint $table)
		{
			$table->dropForeign(['default_responsible']);
		});
	}

};
