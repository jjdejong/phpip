<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDefaultActorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('default_actor', function(Blueprint $table)
		{
			$table->foreign('actor_id', 'fk_dfltactor')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('for_client', 'fk_dfltactor_client')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('for_country', 'fk_dfltactor_country')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('role', 'fk_dfltactor_role')->references('code')->on('actor_role')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('default_actor', function(Blueprint $table)
		{
			$table->dropForeign('fk_dfltactor');
			$table->dropForeign('fk_dfltactor_client');
			$table->dropForeign('fk_dfltactor_country');
			$table->dropForeign('fk_dfltactor_role');
		});
	}

}
