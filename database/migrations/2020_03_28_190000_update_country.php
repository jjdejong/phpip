<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCountry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('country')->where('iso', 'BE')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'BG')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'CY')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'DK')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'FI')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'GR')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'LV')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'LT')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'MK')->update(['renewal_first' => 3,
            'name' => 'North Macedonia',
            'name_FR' => "Macédoine du Nord",
            'name_DE' => "Nordmazedonien"
          ]);
        DB::table('country')->where('iso', 'MT')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'NL')->update(['renewal_first' => 4]);
        DB::table('country')->where('iso', 'PT')->update(['renewal_first' => 5]);
        DB::table('country')->where('iso', 'RO')->update(['renewal_first' => 3]);
        DB::table('country')->where('iso', 'CZ')->update(['name' => 'Czechia', 'name_FR' => 'Tchéquie', 'name_DE' => 'Tschechien']);
        DB::table('country')->where('iso', 'SM')->update(['renewal_first' => 4]);
        DB::table('country')->where('iso', 'SE')->update(['renewal_first' => 3]);

        DB::table('country')->updateOrInsert(
          ['iso' => 'RS'],
          ['numcode' => '895', 'iso3' => 'SRB', 'name_DE' => 'Serbia', 'name' => 'Serbia', 'name_FR' => 'Serbie',
          'ep' => '0', 'wo' => '0', 'renewal_first' => '3', 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]
        );
        DB::table('country')->updateOrInsert(
          ['iso' => 'ME'],
          ['numcode' => '896', 'iso3' => 'MNE', 'name_DE' => 'Montenegro','name' => 'Montenegro','name_FR' => 'Monténégro',
          'ep' => '0', 'wo' => '0', 'renewal_first' => '2', 'renewal_base' => 'FIL', 'renewal_start' => 'FIL', 'checked_on' => Now()]
        );
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
