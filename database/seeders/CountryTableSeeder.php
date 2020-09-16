<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'country.php';
        App\Country::create($country);
    }
}
