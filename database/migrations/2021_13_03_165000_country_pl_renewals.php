<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        DB::table('country')->where('iso', 'PL')->update([
            'renewal_base' => 'FIL',
            'checked_on' => '2021-12-03',
        ]);
    }

    public function down()
    {
        //
    }
};
