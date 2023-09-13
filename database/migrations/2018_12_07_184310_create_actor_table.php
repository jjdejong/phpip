<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActorTable extends Migration {

	public function up()
	{
		Schema::create('actor', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('name', 100)->index('name')->comment('Family name or company name');
			$table->string('first_name', 60)->nullable()->comment('plus middle names, if required');
			$table->string('display_name', 30)->nullable()->unique('uqdisplay_name')->comment('The name displayed in the interface, if not null');
			$table->char('login', 16)->nullable()->unique('uqlogin')->comment('Database user login if not null.');
			$table->string('password', 60)->nullable();
			$table->string('password_salt', 32)->nullable();
			$table->dateTime('last_login')->nullable();
			$table->char('default_role', 5)->nullable()->index('default_role')->comment('Link to actor_role table. A same actor can have different roles - this is the default role of the actor. CAUTION: for database users, this sets the user ACLs.');
			$table->string('function', 45)->nullable();
			$table->unsignedInteger('parent_id')->nullable()->index('parent')->comment('Parent company of this company (another actor), where applicable. Useful for linking several companies owned by a same corporation');
			$table->unsignedInteger('company_id')->nullable()->index('company')->comment('Mainly for inventors and contacts. ID of the actor\'s company or employer (another record in the actors table)');
			$table->unsignedInteger('site_id')->nullable()->index('site')->comment('Mainly for inventors and contacts. ID of the actor\'s company site (another record in the actors table), if the company has several sites that we want to differentiate');
			$table->boolean('phy_person')->default(1)->comment('Physical person or not');
			$table->char('nationality', 2)->nullable()->index('nationality');
			$table->boolean('small_entity')->default(0)->comment('Small entity status used in a few countries (FR, US)');
			$table->string('address', 256)->comment('Main address: street, zip and city')->nullable();
			$table->char('country', 2)->nullable()->index('country')->comment('Country in address');
			$table->string('address_mailing', 256)->comment('Mailing address: street, zip and city')->nullable();
			$table->char('country_mailing', 2)->nullable()->index('country_mailing');
			$table->string('address_billing', 256)->comment('Billing address: street, zip and city')->nullable();
			$table->char('country_billing', 2)->nullable()->index('country_billing');
			$table->string('email', 45)->nullable();
			$table->string('phone', 20)->nullable();
			$table->string('legal_form', 60)->nullable();
			$table->string('registration_no', 20)->nullable();
			$table->boolean('warn')->default(0)->comment('The actor will be displayed in red in the matter view when set');
			$table->text('notes', 65535)->nullable();
			$table->string('VAT_number', 45)->nullable();
			$table->char('creator', 16)->nullable();
			$table->char('updater', 16)->nullable();
			$table->timestamps();
			$table->string('remember_token', 100)->nullable();
		});
	}


	public function down()
	{
		Schema::dropIfExists('actor');
	}

}
