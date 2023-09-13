<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::table('actor', function (Blueprint $table) {
            $table->foreign('company_id')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('country')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('country_billing')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('country_mailing')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('nationality')->references('iso')->on('country')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('parent_id')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('default_role')->references('code')->on('actor_role')->onUpdate('CASCADE')->onDelete('SET NULL');
            $table->foreign('site_id')->references('id')->on('actor')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    public function down()
    {
        Schema::table('actor', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropForeign(['country']);
            $table->dropForeign(['country_billing']);
            $table->dropForeign(['country_mailing']);
            $table->dropForeign(['nationality']);
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['default_role']);
            $table->dropForeign(['site_id']);
        });
    }
};
