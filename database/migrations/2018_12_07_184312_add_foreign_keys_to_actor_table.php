<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToActorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('actor', function(Blueprint $table)
		{
			$table->foreign('company_id', 'fk_actor_company')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('country', 'fk_actor_country')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('country_billing', 'fk_actor_countryb')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('country_mailing', 'fk_actor_countrym')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('nationality', 'fk_actor_nationality')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('parent_id', 'fk_actor_parent')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('default_role', 'fk_actor_role')->references('code')->on('actor_role')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('site_id', 'fk_actor_site')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('actor', function(Blueprint $table)
		{
			$table->dropForeign('fk_actor_company');
			$table->dropForeign('fk_actor_country');
			$table->dropForeign('fk_actor_countryb');
			$table->dropForeign('fk_actor_countrym');
			$table->dropForeign('fk_actor_nationality');
			$table->dropForeign('fk_actor_parent');
			$table->dropForeign('fk_actor_role');
			$table->dropForeign('fk_actor_site');
		});
	}

}
