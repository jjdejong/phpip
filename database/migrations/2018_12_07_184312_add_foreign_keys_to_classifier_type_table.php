<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {

	public function up()
	{
		Schema::table('classifier_type', function(Blueprint $table)
		{
			$table->foreign('for_category')->references('code')->on('matter_category')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	public function down()
	{
		Schema::table('classifier_type', function(Blueprint $table)
		{
			$table->dropForeign(['for_category']);
		});
	}

};
