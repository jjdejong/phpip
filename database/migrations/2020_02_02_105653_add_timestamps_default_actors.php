<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimestampsDefaultActors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('default_actor', function (Blueprint $table) {
            if ( !Schema::hasColumn('default_actor', 'updated_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('default_actor', function (Blueprint $table) {
            if ( Schema::hasColumn('default_actor', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if ( Schema::hasColumn('default_actor', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
}
