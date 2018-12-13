<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDefaultActorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('default_actor', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('actor_id')->unsigned()->index('actor_id');
			$table->char('role', 5)->index('role');
			$table->char('for_category', 5)->nullable();
			$table->char('for_country', 2)->nullable()->index('for_country');
			$table->integer('for_client')->unsigned()->nullable()->index('for_client');
			$table->boolean('shared')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('default_actor');
	}

}
