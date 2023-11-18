<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('template_members', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('template_classes')->onUpdate('CASCADE');
        });
    }

    public function down()
    {
        Schema::table('template_members', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
        });
    }
};
