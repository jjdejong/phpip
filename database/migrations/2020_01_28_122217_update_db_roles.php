<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDbRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('actor_role')->insertOrIgnore([
          ['code' => 'DBA', 'name' => 'DB Administrator'],
          ['code' => 'DBRW', 'name' => 'DB Read/Write'],
          ['code' => 'DBRO', 'name' => 'DB Read-Only']
        ]);

        DB::table('actor')->where('login', 'phpipuser')->update(['default_role' => 'DBA']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Not worth rolling back
    }
}
