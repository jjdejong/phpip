<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClassifierTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('classifier', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->integer('matter_id')->unsigned();
			$table->char('type_code', 5)->index('type')->comment('Link to \'classifier_types\'');
            // 1170 BLOB/TEXT column 'value' used in key specification without a key length (SQL: alter table `classifier` add index `value`(`value`))
            //$table->text('value', 65535)->nullable()->index('value')->comment('A free-text value used when classifier_values has no record linked to the classifier_types record');
            // added after column creation
            $table->text('value', 65535)->nullable()->comment('A free-text value used when classifier_values has no record linked to the classifier_types record');
			$table->string('url', 256)->nullable()->comment('Display value as a link to the URL defined here');
			$table->integer('value_id')->unsigned()->nullable()->index('value_id')->comment('Links to the classifier_values table if it has a link to classifier_types');
			$table->boolean('display_order')->default(1);
			$table->integer('lnk_matter_id')->unsigned()->nullable()->index('lnk_matter')->comment('Matter this case is linked to');
			$table->string('creator', 20)->nullable();
			$table->timestamp('updated')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->string('updater', 20)->nullable();
			// Syntax error or access violation: 1068 Multiple primary key defined (SQL: alter table `classifier` add primary key `classifier_id_matter_id_primary`(`id`, `matter_id`))
            // $table->primary(['id','matter_id']);
			$table->unique(['matter_id','type_code','lnk_matter_id'], 'uqlnk');
			$table->unique(['matter_id','type_code',DB::raw('value(10)')], 'uqvalue');
			$table->unique(['matter_id','type_code','value_id'], 'uqvalue_id');            
			$table->index([DB::raw('value(20)')], 'value');

		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('classifier');
	}

}
