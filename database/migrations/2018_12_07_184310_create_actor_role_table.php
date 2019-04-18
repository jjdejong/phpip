<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActorRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('actor_role', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->char('code', 5)->primary();
			$table->string('name', 45)->index('name');
			$table->boolean('display_order')->nullable()->default(127)->comment('Order of display in interface');
			$table->boolean('shareable')->nullable()->default(0)->comment('Indicates whether actors listed with this role are shareable for all matters of the same family');
			$table->boolean('show_ref')->nullable()->default(0);
			$table->boolean('show_company')->nullable()->default(0);
			$table->boolean('show_rate')->nullable()->default(0);
			$table->boolean('show_date')->nullable()->default(0);
			$table->boolean('box')->nullable()->default(127)->comment('Number of a box in which several roles will be displayed');
			$table->string('box_color', 7)->nullable()->default('#000000')->comment('Color of background');
			$table->string('notes', 160)->nullable();
			$table->string('creator', 20)->nullable();
			$table->timestamp('updated')->nullable()->useCurrent();
			$table->string('updater', 20)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('actor_role');
	}

}
