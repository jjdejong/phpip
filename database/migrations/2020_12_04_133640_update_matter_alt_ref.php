<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMatterAltRef extends Migration
{
    public function up()
    {
        Schema::table('matter', function (Blueprint $table) {
            $table->string('alt_ref', 30)->after('term_adjust')->comment('Alternate reference');
            $table->index('alt_ref');
        });
    }

    public function down()
    {
        Schema::table('matter', function (Blueprint $table) {
            $table->dropColumn('alt_ref');
        });
    }
}
