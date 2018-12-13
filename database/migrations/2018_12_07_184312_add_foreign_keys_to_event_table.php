<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('event', function(Blueprint $table)
		{
			$table->foreign('alt_matter_id', 'fk_event_altmatter')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('matter_id', 'fk_event_matter')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('code', 'fk_event_name')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('event', function(Blueprint $table)
		{
			$table->dropForeign('fk_event_altmatter');
			$table->dropForeign('fk_event_matter');
			$table->dropForeign('fk_event_name');
		});
	}

}
