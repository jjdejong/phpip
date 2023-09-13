<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {

	public function up()
	{
		Schema::create('default_actor', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->unsignedInteger('actor_id')->index('actor_id');
			$table->char('role', 5)->index('role');
			$table->char('for_category', 5)->nullable();
			$table->char('for_country', 2)->nullable()->index('for_country');
			$table->unsignedInteger('for_client')->nullable()->index('for_client');
			$table->boolean('shared')->default(0);
		});
	}


	public function down()
	{
		Schema::dropIfExists('default_actor');
	}

};
