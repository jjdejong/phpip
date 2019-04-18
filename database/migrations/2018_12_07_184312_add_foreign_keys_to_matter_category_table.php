<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMatterCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('matter_category', function(Blueprint $table)
		{
			$table->foreign('display_with')->references('code')->on('matter_category')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('matter_category', function(Blueprint $table)
		{
			$table->dropForeign(['display_with']);
		});
	}

}
