<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::table('matter', function (Blueprint $table) {
            $table->foreign('category_code')->references('code')->on('matter_category')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('container_id')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('country')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('origin')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('parent_id')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('responsible')->references('login')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('type_code')->references('code')->on('matter_type')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    public function down()
    {
        Schema::table('matter', function (Blueprint $table) {
            $table->dropForeign(['category_code']);
            $table->dropForeign(['container_id']);
            $table->dropForeign(['country']);
            $table->dropForeign(['origin']);
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['responsible']);
            $table->dropForeign(['type_code']);
        });
    }
};
