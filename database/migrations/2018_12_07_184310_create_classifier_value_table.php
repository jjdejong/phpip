<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClassifierValueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('classifier_value', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('value', 160);
			$table->char('type_code', 5)->nullable()->index('value_type')->comment('Restrict this classifier name to the classifier type identified here');
			$table->string('notes')->nullable();
			$table->string('creator', 20)->nullable();
			$table->timestamp('updated')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('updater', 20)->nullable();
			$table->unique(['value','type_code'], 'uqclvalue');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('classifier_value');
	}

}
