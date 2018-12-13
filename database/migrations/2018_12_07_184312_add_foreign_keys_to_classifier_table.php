<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToClassifierTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('classifier', function(Blueprint $table)
		{
			$table->foreign('lnk_matter_id', 'fk_lnkmatter')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('matter_id', 'fk_matter')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('type_code', 'fk_type')->references('code')->on('classifier_type')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('value_id', 'fk_value')->references('id')->on('classifier_value')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('classifier', function(Blueprint $table)
		{
			$table->dropForeign('fk_lnkmatter');
			$table->dropForeign('fk_matter');
			$table->dropForeign('fk_type');
			$table->dropForeign('fk_value');
		});
	}

}
