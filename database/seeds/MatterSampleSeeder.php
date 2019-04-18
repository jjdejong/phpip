<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
        require 'matter_actor_lnk-sample.php';
        DB::table('matter_actor_lnk')->insert($matter_actor_lnk);
    }
}
