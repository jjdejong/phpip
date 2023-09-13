<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActorRoleTable extends Migration {

	public function up()
	{
		Schema::create('actor_role', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->char('code', 5)->primary();
			$table->string('name', 45)->index('name');
			$table->boolean('display_order')->nullable()->default(127)->comment('Order of display in interface');
			$table->boolean('shareable')->default(0)->comment('Indicates whether actors listed with this role are shareable for all matters of the same family');
			$table->boolean('show_ref')->default(0);
			$table->boolean('show_company')->default(0);
			$table->boolean('show_rate')->default(0);
			$table->boolean('show_date')->default(0);
			$table->string('notes', 160)->nullable();
			$table->string('creator', 20)->nullable();
			$table->string('updater', 20)->nullable();
			$table->timestamps();
		});
	}


	public function down()
	{
		Schema::dropIfExists('actor_role');
	}

}
