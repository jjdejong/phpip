<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMatterActorLnkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('matter_actor_lnk', function(Blueprint $table)
		{
			$table->foreign('actor_id', 'fk_lnk_actor')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('company_id', 'fk_lnk_company')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('matter_id', 'fk_lnk_matter')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('role', 'fk_lnk_role')->references('code')->on('actor_role')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('matter_actor_lnk', function(Blueprint $table)
		{
			$table->dropForeign('fk_lnk_actor');
			$table->dropForeign('fk_lnk_company');
			$table->dropForeign('fk_lnk_matter');
			$table->dropForeign('fk_lnk_role');
		});
	}

}
