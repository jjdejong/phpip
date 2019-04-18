<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('event', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->char('code', 5)->index('code')->comment('Link to event_names table');
			$table->integer('matter_id')->unsigned();
			$table->date('event_date')->nullable()->index('date');
			$table->integer('alt_matter_id')->unsigned()->nullable()->index('alt_matter')->comment('Essentially for priority claims. ID of prior patent this event refers to');
			$table->string('detail', 45)->nullable()->index('number')->comment('Numbers or short comments');
			$table->string('notes', 150)->nullable();
			$table->char('creator', 16)->nullable();
			$table->timestamp('updated')->nullable()->useCurrent();
			$table->char('updater', 16)->nullable();
			$table->unique(['matter_id','code','event_date','alt_matter_id'], 'uqevent');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('event');
	}

}
