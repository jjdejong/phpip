<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDefaultActorTable extends Migration {

	public function up()
	{
		Schema::table('default_actor', function(Blueprint $table)
		{
			$table->foreign('actor_id')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('for_client')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('for_country')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('role')->references('code')->on('actor_role')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	public function down()
	{
		Schema::table('default_actor', function(Blueprint $table)
		{
			$table->dropForeign(['actor_id']);
			$table->dropForeign(['for_client']);
			$table->dropForeign(['for_country']);
			$table->dropForeign(['role']);
		});
	}

}
