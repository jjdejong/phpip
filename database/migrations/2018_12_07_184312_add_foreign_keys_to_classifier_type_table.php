<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToClassifierTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('classifier_type', function(Blueprint $table)
		{
			$table->foreign('for_category', 'fk_forcategory')->references('code')->on('matter_category')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('classifier_type', function(Blueprint $table)
		{
			$table->dropForeign('fk_forcategory');
		});
	}

}
