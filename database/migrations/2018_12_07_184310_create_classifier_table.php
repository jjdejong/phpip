<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClassifierTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classifier', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('matter_id');
            $table->char('type_code', 5)->index('type')->comment('Link to \'classifier_types\'');
            // 1170 BLOB/TEXT column 'value' used in key specification without a key length (SQL: alter table `classifier` add index `value`(`value`))
            //$table->text('value', 65535)->nullable()->index('value')->comment('A free-text value used when classifier_values has no record linked to the classifier_types record');
            // added after column creation, see bottom
            $table->text('value', 65535)->nullable()->comment('A free-text value used when classifier_values has no record linked to the classifier_types record');
            $table->string('url', 256)->nullable()->comment('Display value as a link to the URL defined here');
            $table->unsignedInteger('value_id')->nullable()->index('value_id')->comment('Links to the classifier_values table if it has a link to classifier_types');
            $table->boolean('display_order')->default(1);
            $table->unsignedInteger('lnk_matter_id')->nullable()->index('lnk_matter')->comment('Matter this case is linked to');
            $table->string('creator', 20)->nullable();
            $table->string('updater', 20)->nullable();
            $table->timestamps();

            $table->unique(['matter_id', 'type_code', 'lnk_matter_id'], 'uqlnk');
            $table->unique(['matter_id', 'type_code', DB::raw('value(30)')], 'uqvalue');
            $table->unique(['matter_id', 'type_code', 'value_id'], 'uqvalue_id');
            $table->index([DB::raw('value(20)')], 'value');
        });
        Schema::table('classifier', function ($table) {
            DB::statement('ALTER TABLE classifier MODIFY id INT UNSIGNED AUTO_INCREMENT'); // add auto increment
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classifier');
    }
}
