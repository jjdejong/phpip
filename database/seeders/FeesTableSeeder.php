<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Fee;

class FeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'fees.php';
        Fee::insertOrIgnore($fees);
    }
}
