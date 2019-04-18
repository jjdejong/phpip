<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTaskRulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('task_rules', function(Blueprint $table)
		{
			$table->foreign('abort_on')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('for_category')->references('code')->on('matter_category')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('condition_event')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('for_country')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('task')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('for_origin')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('trigger_event')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('task_rules', function(Blueprint $table)
		{
			$table->dropForeign(['abort_on']);
			$table->dropForeign(['for_category']);
			$table->dropForeign(['condition_event']);
			$table->dropForeign(['for_country']);
			$table->dropForeign(['task']);
			$table->dropForeign(['for_origin']);
			$table->dropForeign(['trigger_event']);
		});
	}

}
