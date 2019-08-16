<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMatterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('matter', function(Blueprint $table)
		{
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->char('category_code', 5)->index('category');
			$table->string('caseref', 30)->comment('Case reference for the database user. The references for the other actors (client, agent, etc.) are in the actor link table.');
			$table->char('country', 2)->index('country')->comment('Country where the matter is filed');
			$table->char('origin', 2)->nullable()->index('origin')->comment('Code of the regional system the patent originates from (mainly EP or WO)');
			$table->char('type_code', 5)->nullable()->index('type');
			$table->boolean('idx')->nullable()->comment('Increment this to differentiate multiple patents filed in the same country in the same family');
			$table->string('suffix', 16)->virtualAs('concat_ws("",concat_ws("-",concat_ws("/",`country`,`origin`),`type_code`),`idx`)');
			$table->unsignedInteger('parent_id')->nullable()->index('parent')->comment('Link to parent patent. Used to create a hierarchy');
			$table->unsignedInteger('container_id')->nullable()->index('container')->comment('Identifies the container matter from which this matter gathers its shared data. If null, this matter is a container');
			$table->char('responsible', 16)->index('responsible')->comment('Database user responsible for the patent');
			$table->boolean('dead')->default(0)->comment('Indicates that the case is no longer supervised. Automatically set by "killer events" like "Abandoned"');
			$table->text('notes', 65535)->nullable();
			$table->date('expire_date')->nullable();
			$table->smallInteger('term_adjust')->default(0)->comment('Patent term adjustment in days. Essentially for US patents.');
			$table->char('creator', 16)->nullable()->comment('User who created the record');
			$table->char('updater', 16)->nullable()->comment('User who last modified the record');
			$table->timestamps();
			$table->index(['caseref','container_id','origin','country','type_code','idx'], 'sort');
			$table->unique(['category_code','caseref','suffix'], 'UID');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('matter');
	}

}
