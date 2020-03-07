<?php

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $languages = [
        [
        'language' => 'Français',
        'code' => 'fr'
        ],
        [
          'language' => 'English',
          'code' => 'en'
        ],
        [
          'language' => 'Deutsch',
          'code' => 'de'
        ]
      ];
          DB::table('languages')->insert($languages);
    }
}
