<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::create('matter_type', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->char('code', 5)->primary();
            $table->string('type', 45);
            $table->string('creator', 20)->nullable();
            $table->string('updater', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('matter_type');
    }
};
