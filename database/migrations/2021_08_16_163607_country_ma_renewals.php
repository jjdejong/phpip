<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CountryMaRenewals extends Migration
{
    public function up()
    {
        DB::table('country')->where('iso', 'MA')->update([
            'renewal_base' => 'FIL',
            'checked_on' => '2021-08-16'
        ]);
    }

    public function down()
    {
        //
    }
}
