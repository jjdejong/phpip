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
			$table->foreign('abort_on', 'fk_abort_on')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('for_category', 'fk_category')->references('code')->on('matter_category')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('condition_event', 'fk_condition')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('for_country', 'fk_country')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('task', 'fk_name')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('for_origin', 'fk_origin')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('trigger_event', 'fk_trigger')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
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
			$table->dropForeign('fk_abort_on');
			$table->dropForeign('fk_category');
			$table->dropForeign('fk_condition');
			$table->dropForeign('fk_country');
			$table->dropForeign('fk_name');
			$table->dropForeign('fk_origin');
			$table->dropForeign('fk_trigger');
		});
	}

}
