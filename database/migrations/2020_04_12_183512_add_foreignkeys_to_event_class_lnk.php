<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignkeysToEventClassLnk extends Migration
{
    public function up()
    {
        Schema::table('event_class_lnk', function (Blueprint $table) {
            $table->foreign('template_class_id')->references('id')->on('template_classes')->onUpdate('CASCADE')->onDelete('RESTRICT');
            $table->foreign('event_name_code')->references('code')->on('event_name')->onUpdate('CASCADE')->onDelete('RESTRICT');
        });
    }

    public function down()
    {
        Schema::table('event_class_lnk', function (Blueprint $table) {
            $table->dropForeign(['template_class_id']);
            $table->dropForeign(['event_name_code']);
        });
    }
}
