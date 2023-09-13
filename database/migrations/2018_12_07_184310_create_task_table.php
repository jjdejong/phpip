<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {

	public function up()
	{
		Schema::create('task', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->unsignedInteger('trigger_id')->index('trigger_id')->comment('Link to generating event');
			$table->char('code', 5)->index('code')->comment('Task code. Link to event_names table');
			$table->date('due_date')->index('due_date');
			$table->char('assigned_to', 16)->nullable()->index('responsible')->comment('User responsible for the task (if not the user responsible for the case)');
			$table->string('detail', 45)->nullable()->index('detail')->comment('Numbers or short comments');
			$table->boolean('done')->nullable()->default(0)->comment('Set to 1 when task done');
			$table->date('done_date')->nullable()->comment('Optional task completion date');
			$table->unsignedInteger('rule_used')->nullable()->index('task_rule')->comment('ID of the rule that was used to set this task');
			$table->time('time_spent')->nullable()->comment('Time spent by attorney on task');
			$table->string('notes', 150)->nullable();
			$table->decimal('cost', 6)->nullable()->comment('The estimated or invoiced fee amount');
			$table->decimal('fee', 6)->nullable();
			$table->char('currency', 3)->nullable()->default('EUR');
			$table->char('creator', 16)->nullable();
			$table->char('updater', 16)->nullable();
			$table->timestamps();
		});
	}


	public function down()
	{
		Schema::dropIfExists('task');
	}

};
