<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_members', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('class_id')->comment('The class which allow to link the template to an event or a task');
            $table->string('language', 2)->comment('Code of the language for the document');
            $table->string('style', 30)->nullable()->comment('Help to distinguish documents in a same class. Free text');
            $table->string('category', 30)->nullable()->comment('Help to classify documents. Free text');
            $table->string('format', 4);
            $table->string('summary', 255)->comment('The label of the document as displayed in lists');
            $table->string('subject', 160)->comment('It can content fields to merge at creation time');
            $table->text('body')->comment('It can content fields to merge at creation time, and HTML tags when this format is chosen');
            $table->string('creator', 20);
            $table->string('updater', 20);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('template_members');
    }
};
