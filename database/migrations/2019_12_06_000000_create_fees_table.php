<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fees', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->char('for_category', 5)->default('PAT')->index('fk_category')->comment('Category to which this rule applies.');
			$table->char('for_country', 2)->nullable()->index('for_country')->comment('Country where rule is applicable. If NULL, applies to all countries');
			$table->char('for_origin', 5)->nullable()->index('for_origin');
			$table->integer('qt')->default(0)->comment('For which renewal');
			$table->date('use_before')->nullable()->comment('will be used only if the due date is before this date');
			$table->date('use_after')->nullable()->comment('will be used only if the due date is after this date');
			$table->decimal('cost', 6)->nullable();
			$table->decimal('fee', 6)->nullable();
			$table->decimal('cost_reduced', 6)->nullable();
			$table->decimal('fee_reduced', 6)->nullable();
			$table->decimal('cost_sup', 6)->nullable();
			$table->decimal('fee_sup', 6)->nullable();
			$table->decimal('cost_sup_reduced', 6)->nullable();
			$table->decimal('fee_sup_reduced', 6)->nullable();
			$table->char('currency', 3)->nullable()->default('EUR');
			$table->char('creator', 16)->nullable();
			$table->char('updater', 16)->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('fees');
	}

}
