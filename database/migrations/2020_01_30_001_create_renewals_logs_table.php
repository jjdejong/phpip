<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up()
    {
        Schema::create('renewals_logs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('task_id')->index();
            $table->unsignedInteger('job_id');
            $table->tinyInteger('from_step')->nullable()->default(null)->comment('Step before the job');
            $table->tinyInteger('to_step')->nullable()->default(null)->comment('Step after the job');
            $table->tinyInteger('from_grace')->nullable()->default(null)->comment('Grace state before the job');
            $table->tinyInteger('to_grace')->nullable()->default(null)->comment('Grace state after the job');
            $table->tinyInteger('from_invoice')->nullable()->default(null)->comment('Invoice state before the job');
            $table->tinyInteger('to_invoice')->nullable()->default(null)->comment('Invoice state after the job');
            $table->tinyInteger('from_done')->nullable()->default(null)->comment('Done state before the job');
            $table->tinyInteger('to_done')->nullable()->default(null)->comment('Done state after the job');
            $table->string('creator', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('renewals_logs');
    }
};
