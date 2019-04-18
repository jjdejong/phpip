<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMatterTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matter_type', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->char('code', 5)->primary();
			$table->string('type', 45);
			$table->string('creator', 20)->nullable();
			$table->timestamp('updated')->useCurrent();
			$table->string('updater', 20)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('matter_type');
	}

}
