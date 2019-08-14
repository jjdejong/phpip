<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMatterActorLnkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matter_actor_lnk', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('matter_id')->unsigned();
			$table->integer('actor_id')->unsigned()->index('actor_lnk');
			$table->boolean('display_order')->default(1)->comment('Order in which the actor should be displayed in a list of same type actors');
			$table->char('role', 5)->index('role_lnk');
			$table->boolean('shared')->default(0)->comment('Copied from the actor_role.shareable field. Indicates that this information, stored in the "container", is shared among members of the same family');
			$table->string('actor_ref', 45)->nullable()->index('actor_ref')->comment('Actor\'s reference');
			$table->integer('company_id')->unsigned()->nullable()->index('company_lnk')->comment('A copy of the actor\'s company ID, if applicable, at the time the link was created.');
			$table->decimal('rate', 5)->nullable()->default(100.00)->comment('For co-owners - rate of ownership, or inventors');
			$table->date('date')->nullable()->comment('A date field that can, for instance, contain the date of ownership acquisition');
			$table->char('creator', 16)->nullable();
			$table->char('updater', 16)->nullable();
			$table->timestamps();
			$table->unique(['matter_id','role','actor_id'], 'uqactor_role');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('matter_actor_lnk');
	}

}
