<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignkeysToTemplateMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_members', function (Blueprint $table) {
          $table->foreign('class_id')->references('id')->on('template_classes')->onUpdate('CASCADE');
          $table->foreign('language_id')->references('id')->on('languages')->onUpdate('CASCADE');
          $table->foreign('style_id')->references('id')->on('template_styles')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_members', function (Blueprint $table) {
      			$table->dropForeign(['class_id']);
      			$table->dropForeign(['language_id']);
      			$table->dropForeign(['style_id']);
        });
    }
}
