<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskClassLnk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

      Schema::create('task_class_lnk', function (Blueprint $table) {
          $table->increments('id');
          $table->unsignedInteger('task_rule_id');
          $table->unsignedInteger('template_class_id');
          $table->timestamps();

          $table->foreign('template_class_id')->references('id')->on('template_classes')->onUpdate('CASCADE');
          $table->foreign('task_rule_id')->references('id')->on('task_rules')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('task_class_lnk', function (Blueprint $table) {
          $table->dropForeign(['task_rule_id']);
          $table->dropForeign(['template_class_id']);
      });
      Schema::dropIfExists('task_class_lnk');
    }
}
