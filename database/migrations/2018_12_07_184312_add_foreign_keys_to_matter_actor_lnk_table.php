<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::table('matter_actor_lnk', function (Blueprint $table) {
            $table->foreign('actor_id')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('company_id')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('matter_id')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('role')->references('code')->on('actor_role')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    public function down()
    {
        Schema::table('matter_actor_lnk', function (Blueprint $table) {
            $table->dropForeign(['actor_id']);
            $table->dropForeign(['company_id']);
            $table->dropForeign(['matter_id']);
            $table->dropForeign(['role']);
        });
    }
};
