<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::table('classifier', function (Blueprint $table) {
            $table->foreign('lnk_matter_id')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('matter_id')->references('id')->on('matter')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('type_code')->references('code')->on('classifier_type')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('value_id')->references('id')->on('classifier_value')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    public function down()
    {
        Schema::table('classifier', function (Blueprint $table) {
            $table->dropForeign(['lnk_matter_id']);
            $table->dropForeign(['matter_id']);
            $table->dropForeign(['type_code']);
            $table->dropForeign(['value_id']);
        });
    }
};
