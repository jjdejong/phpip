<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignkeysToTemplateClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('template_classes', function (Blueprint $table) {
        		$table->foreign('category_id')->references('id')->on('template_categories')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('default_role')->references('code')->on('actor_role')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('template_classes', function (Blueprint $table) {
      			$table->dropForeign(['category_id']);
        		$table->dropForeign(['default_role']);
        });
    }
}
