<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CountryPlRenewals extends Migration
{
    public function up()
    {
        DB::table('country')->where('iso', 'PL')->update([
            'renewal_base' => 'FIL',
            'checked_on' => '2021-12-03'
        ]);
    }

    public function down()
    {
        //
    }
}
