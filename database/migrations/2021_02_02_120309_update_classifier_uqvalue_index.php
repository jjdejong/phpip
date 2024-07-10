<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('classifier', function (Blueprint $table) {
            $indexes = Schema::getIndexes('classifier');
            if (array_key_exists('uqvalue', $indexes)) {
                $table->dropIndex('uqvalue');
            }
            $table->unique(['matter_id', 'type_code', DB::raw('value(30)')], 'uqvalue');
        });
    }

    public function down()
    {
        //
    }
};
