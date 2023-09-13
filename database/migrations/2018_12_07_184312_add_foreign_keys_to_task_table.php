<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTaskTable extends Migration {

	public function up()
	{
		Schema::table('task', function(Blueprint $table)
		{
			$table->foreign('assigned_to')->references('login')->on('actor')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('code')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('rule_used')->references('id')->on('task_rules')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('trigger_id')->references('id')->on('event')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	public function down()
	{
		Schema::table('task', function(Blueprint $table)
		{
			$table->dropForeign(['assigned_to']);
			$table->dropForeign(['code']);
			$table->dropForeign(['rule_used']);
			$table->dropForeign(['trigger_id']);
		});
	}

}
