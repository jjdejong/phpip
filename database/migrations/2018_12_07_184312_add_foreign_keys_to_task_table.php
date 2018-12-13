<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('task', function(Blueprint $table)
		{
			$table->foreign('assigned_to', 'fk_assigned_to')->references('login')->on('actor')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('code', 'fk_task_code')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('rule_used', 'fk_task_rule')->references('id')->on('task_rules')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('trigger_id', 'fk_trigger_id')->references('id')->on('event')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('task', function(Blueprint $table)
		{
			$table->dropForeign('fk_assigned_to');
			$table->dropForeign('fk_task_code');
			$table->dropForeign('fk_task_rule');
			$table->dropForeign('fk_trigger_id');
		});
	}

}
