<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventNameTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event_name', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->char('code', 5)->primary();
			$table->string('name', 45);
			$table->char('category', 5)->nullable()->comment('Category to which this event is specific');
			$table->char('country', 2)->nullable()->comment('Country to which the event is specific. If NULL, any country may use the event');
			$table->boolean('is_task')->default(0)->comment('Indicates whether the event is a task');
			$table->boolean('status_event')->default(0)->comment('Indicates whether the event should be displayed as a status');
			$table->char('default_responsible', 16)->nullable()->index('fk_responsible')->comment('Login of the user who is systematically responsible for this type of task');
			$table->boolean('use_matter_resp')->default(0)->comment('Set if the matter responsible should also be set as responsible for the task. Overridden if default_responsible is set');
			$table->boolean('unique')->default(0)->comment('Only one such event can exist');
			$table->boolean('uqtrigger')->default(0)->comment('Can only be triggered by one event');
			$table->boolean('killer')->default(0)->comment('Indicates whether this event kills the patent (set patent.dead to 1)');
			$table->string('notes', 160)->nullable();
			$table->char('creator', 16)->nullable();
			$table->timestamp('updated')->nullable()->useCurrent();
			$table->char('updater', 16)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('event_name');
	}

}
