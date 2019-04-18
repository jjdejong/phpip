<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMatterCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matter_category', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->char('code', 5)->primary();
			$table->string('ref_prefix', 5)->nullable()->comment('Used to build the case reference');
			$table->string('category', 45);
			$table->char('display_with', 5)->default('PAT')->index('display_with')->comment('Display with the indicated category in the interface');
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
		Schema::drop('matter_category');
	}

}
