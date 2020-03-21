<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemplateClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('template_classes', function(Blueprint $table)
        {
    			$table->engine = 'InnoDB';
    			$table->increments('id');
    			$table->string('name',55);
          $table->string('description',255)->nullable();
          $table->unsignedInteger('category_id')->nullable()->comment('Help to classify documents');
          $table->string('default_role',5)->nullable()->default(null)->comment('Role of actor who is the receiver of the document');
          $table->string('creator',20);
          $table->string('updater',20);
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
  		Schema::dropIfExists('template_classes');
    }
}
