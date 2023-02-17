<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::table('country')->insertOrIgnore([

            [
                'numcode' => 0,
                'iso' => 'AP',
                'iso3' => '',
                'name_DE' => '',
                'name' => 'African Regional Intellectual Property Organization',
                'name_FR' => 'Organisation régionale africaine de la propriété intellectuelle',
                'ep' => 0,
                'wo' => 0,
                'em' => 0,
                'oa' => 0,
                'renewal_first' => 2,
                'renewal_base' => 'FIL',
                'renewal_start' => 'FIL',
                'checked_on' => '2023-02-16',
            ],
            [
                'numcode' => 0,
                'iso' => 'EA',
                'iso3' => '',
                'name_DE' => '',
                'name' => 'Eurasian Patent Organization',
                'name_FR' => 'Organisation eurasienne des brevets',
                'ep' => 0,
                'wo' => 0,
                'em' => 0,
                'oa' => 0,
                'renewal_first' => 2,
                'renewal_base' => 'FIL',
                'renewal_start' => 'GRT',
                'checked_on' => '2023-02-16',
            ]
        ]);
    }
};
