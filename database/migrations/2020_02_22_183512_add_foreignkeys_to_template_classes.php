<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('template_classes', function (Blueprint $table) {
            $table->foreign('default_role')->references('code')->on('actor_role')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    public function down()
    {
        Schema::table('template_classes', function (Blueprint $table) {
            $table->dropForeign(['default_role']);
        });
    }
};
