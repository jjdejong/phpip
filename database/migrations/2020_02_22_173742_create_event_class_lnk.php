<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('event_class_lnk', function (Blueprint $table) {
            $table->increments('id');
            $table->string('event_name_code',5);
            $table->unsignedInteger('template_class_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('event_class_lnk');
    }
};
