<?php

use Illuminate\Database\Seeder;

class MatterSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        require 'matter-sample.php';
        App\Matter::insert($matter);
    }
}
