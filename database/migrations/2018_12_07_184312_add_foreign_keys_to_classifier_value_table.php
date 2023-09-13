<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {

	public function up()
	{
		Schema::table('classifier_value', function(Blueprint $table)
		{
			$table->foreign('type_code')->references('code')->on('classifier_type')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	public function down()
	{
		Schema::table('classifier_value', function(Blueprint $table)
		{
			$table->dropForeign(['type_code']);
		});
	}

};
