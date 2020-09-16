<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Country;

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
        Country::insertOrIgnore($country);
    }
}
