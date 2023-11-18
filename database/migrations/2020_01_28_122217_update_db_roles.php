<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        DB::table('actor_role')->insertOrIgnore([
            ['code' => 'DBA', 'name' => 'DB Administrator'],
            ['code' => 'DBRW', 'name' => 'DB Read/Write'],
            ['code' => 'DBRO', 'name' => 'DB Read-Only'],
        ]);

        DB::table('actor')->where('login', 'phpipuser')->update(['default_role' => 'DBA']);

    }

    public function down()
    {
        // Not worth rolling back
    }
};
