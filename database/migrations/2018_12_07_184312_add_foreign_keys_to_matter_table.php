<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMatterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('matter', function(Blueprint $table)
		{
			$table->foreign('category_code', 'fk_matter_category')->references('code')->on('matter_category')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('container_id', 'fk_matter_container')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('country', 'fk_matter_country')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('origin', 'fk_matter_origin')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('parent_id', 'fk_matter_parent')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('responsible', 'fk_matter_responsible')->references('login')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('type_code', 'fk_matter_type')->references('code')->on('matter_type')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('matter', function(Blueprint $table)
		{
			$table->dropForeign('fk_matter_category');
			$table->dropForeign('fk_matter_container');
			$table->dropForeign('fk_matter_country');
			$table->dropForeign('fk_matter_origin');
			$table->dropForeign('fk_matter_parent');
			$table->dropForeign('fk_matter_responsible');
			$table->dropForeign('fk_matter_type');
		});
	}

}
