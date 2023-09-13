<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTables2 extends Migration
{
    public function up()
    {
      Schema::table('matter', function (Blueprint $table) {
        $table->dropIndex('UID');
        $table->string('uid', 45)->after('suffix')->virtualAs('concat(`caseref`,`suffix`)');
        $table->index('uid', 'uid');
      });
      Schema::table('actor', function (Blueprint $table) {
        $table->dropColumn('password_salt');
        $table->dropColumn('last_login');
      });
    }

    public function down()
    {
      Schema::table('matter', function (Blueprint $table) {
        $table->dropColumn('uid');
        $table->unique(['category_code','caseref','suffix'], 'UID');
      });
      Schema::table('actor', function (Blueprint $table) {
        $table->string('password_salt', 32)->after('password')->nullable();
        $table->dateTime('last_login')->after('password_salt')->nullable();
      });
    }
}
