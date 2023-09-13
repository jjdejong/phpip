<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('country')->insertOrIgnore([

            [
                'numcode' => 0,
                'iso' => 'UP',
                'iso3' => 'UPA',
                'name_DE' => 'Einheitlichen Patentschutz',
                'name' => 'Unitary Patent',
                'name_FR' => 'Brevet Unitaire',
                'ep' => 1,
                'wo' => 0,
                'em' => 0,
                'oa' => 0,
                'renewal_first' => 1,
                'renewal_base' => 'FIL',
                'renewal_start' => 'GRT',
                'checked_on' => '2023-05-22',
            ],
        ]);
    }
};
