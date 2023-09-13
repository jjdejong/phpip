<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {

	public function up()
	{
		Schema::create('country', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->smallInteger('numcode')->nullable();
			$table->char('iso', 2)->primary();
			$table->char('iso3', 3)->nullable();
			$table->string('name_DE', 80)->nullable();
			$table->string('name', 80);
			$table->string('name_FR', 80)->nullable();
			$table->boolean('ep')->nullable()->default(0)->comment('Flag default countries for EP ratifications');
			$table->boolean('wo')->nullable()->default(0)->comment('Flag default countries for PCT national phase');
			$table->boolean('em')->nullable()->default(0)->comment('Flag default countries for EU trade mark');
			$table->boolean('oa')->nullable()->default(0)->comment('Flag default countries for OA national phase');
		});
	}


	public function down()
	{
		Schema::dropIfExists('country');
	}

};
