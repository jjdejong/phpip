<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToClassifierValueTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('classifier_value', function(Blueprint $table)
		{
			$table->foreign('type_code', 'fk_value_type')->references('code')->on('classifier_type')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('classifier_value', function(Blueprint $table)
		{
			$table->dropForeign('fk_value_type');
		});
	}

}
